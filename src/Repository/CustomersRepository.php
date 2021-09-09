<?php

namespace App\Repository;

use App\Entity\Customers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customers[]    findAll()
 * @method Customers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersRepository extends ServiceEntityRepository
{
    /**
     * @var int
     */
    private $iteration = 0;

    /**
     * Max customers batch size before flush
     */
    const BATCH_SIZE = 100;

    /**
     * CustomersRepository constructor
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customers::class);
    }

    /**
     * Save customers to database
     *
     * @param Customers|null $customer
     */
    public function saveCustomerBatch(?Customers $customer)
    {
        $this->iteration++;

        if ($customer === null) {
            $this->_em->flush();
            $this->_em->clear();
            return;
        }

        $this->_em->persist($customer);

        if (($this->iteration % self::BATCH_SIZE) === 0) {
            $this->_em->flush();
        }
    }

    /**
     * Return array of Customers entity
     *
     * @param array $emails
     * @return array
     */
    public function getCustomersByEmails(array $emails): array
    {
        $oldCustomers = $this->_em
            ->getRepository(Customers::class)
            ->findBy(['email' => $emails]);

        if (!$oldCustomers) {
            return [];
        }

        $out = [];
        foreach ($oldCustomers as $customer) {
            $out[$customer->getEmail()] = $customer;
        }

        return $out;
    }

}
