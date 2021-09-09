<?php

namespace App\Service;

use App\Entity\Customers;
use App\Provider\CustomersProviderInterface;
use App\Repository\CustomersRepository;
use App\Serializer\CustomersDenormalizerInterface;
use App\Serializer\RandomUserToCustomersDenormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class CustomersImporter {

    /**
     * @var CustomersProviderInterface
     */
    private $provider;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var CustomersRepository
     */
    private $repo;

    /**
     * @var ContextAwareDenormalizerInterface
     */
    private $denormalizer;

    public function __construct(
        CustomersProviderInterface $provider,
        CustomersDenormalizerInterface $denormalizer,
        CustomersRepository $customersRepository,
        array $providerParams = [])
    {
        $this->provider     = $provider;
        $this->denormalizer = $denormalizer;
        $this->serializer   = new Serializer([$this->denormalizer], []);
        $this->repo         = $customersRepository;

        if ($providerParams) {
            $this->provider->setParams($providerParams);
        }
    }

    /**
     * Set provider base url
     *
     * @param string $url
     * @return $this
     */
    public function setBaseUrl(string $url): self
    {
        $this->provider->setBaseUrl($url);

        return $this;
    }

    /**
     * Fetch customers by data provider and save it to database
     *
     * @param string $nationality
     * @param int $count
     * @return array
     */
    public function import(string $nationality = CustomersProviderInterface::NATIONALITY_AU, int $count = 100): array
    {
        $stats = [
            'inserted' => 0,
            'updated' => 0,
        ];

        // get batch of customers array
        foreach ($this->provider->loadByNationality($nationality, $count) as $customersArray) {

            /** @var array $emails */
            $emails = $this->provider->getLoadedEmails();

            // try to fetch customers by emails
            $oldCustomers = $this->repo->getCustomersByEmails($emails);

            // process each customer
            foreach ($customersArray as $rawCustomerData) {

                $currentEmail = $this->denormalizer->getEmail($rawCustomerData);
                $context      = isset($oldCustomers[$currentEmail])
                    ? ['object_to_populate' => $oldCustomers[$currentEmail]]
                    : [];

                // denormalize to Customers entity (to detached or to managed (if it was found by email))
                $customer = $this->serializer->denormalize(
                    $rawCustomerData,
                    Customers::class,
                    null,
                    $context
                );

                $stats[$context ? 'updated' : 'inserted']++;

                $this->repo->saveCustomerBatch($customer);
            }
        }

        // save last small batch of customers if exist
        $this->repo->saveCustomerBatch(null);

        return $stats;
    }


}