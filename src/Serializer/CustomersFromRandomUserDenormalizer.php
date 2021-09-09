<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Customers;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Serializer\CustomersDenormalizerInterface;

/**
 * Prepare and denormalize array to App\Entity\Customers object
 */
class CustomersFromRandomUserDenormalizer implements CustomersDenormalizerInterface
{
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * Prepare array with one customer data from RandomUser API response
     * and denormalize it to Customers object
     *
     * @param mixed $customerData
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return Customers
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function denormalize(
        $customerData,
        string $type = Customers::class,
        string $format = null,
        array $context = []): Customers
    {
        // prepare RandomUser API data
        // fullName
        $customerData['fullName'] = $customerData['name']['first'] . ' ' . $customerData['name']['last'];
        // username
        $customerData['username'] = $customerData['login']['username'];
        // city and country
        $customerData['city']     = $customerData['location']['city'];
        $customerData['country']  = $customerData['location']['country'];
        // remove id
        unset($customerData['id']);

        return $this->normalizer->denormalize($customerData, $type, $format, $context);
    }

    /**
     * Check is supported object for denormalization
     *
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return bool
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return (new $type()) instanceof Customers;
    }

    /**
     * Return email from raw customer data
     *
     * @param array $rawCustomerData
     * @return string
     */
    public function getEmail(array $rawCustomerData): string
    {
        return $rawCustomerData['email'];
    }
}