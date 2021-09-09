<?php

namespace Helper;

use App\Entity\Customers;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

class Factories extends \Codeception\Module
{
    /**
     * @var FactoryMuffin
     */
    private $factory;

    /**
     * Created entities array
     *
     * @var array
     */
    private $createdEntities = [];

    /**
     * Define factories
     *
     * @param array $settings
     * @return void
     * @throws \Codeception\Exception\ModuleException
     */
    public function _beforeSuite($settings = []): void
    {
        $this->factory = $this->getModule('DataFactory');

        $this->factory->_define(Customers::class, [
            'fullName' => Faker::name(),
            'email'    => Faker::email(),
            'country'  => Faker::country(100),
            'username' => Faker::username(),
            'gender'   => ['male', 'female'][mt_rand(0,1)],
            'city'     => Faker::city(),
            'phone'    => Faker::phoneNumber(),
        ]);
    }

    /**
     * Create one customer and save it to database
     *
     * @return mixed
     */
    public function haveCustomer(): Customers
    {
        return $this->haveCustomers(1)[0];
    }

    /**
     * Create customers and save it to database
     *
     * @param int $num
     * @return Customers[]
     */
    public function haveCustomers($num = 1): array
    {
        return $this->multiple(Customers::class, $num);
    }

    /**
     * Create miltiple entities and save it for later
     *
     * @param string $entityClass
     * @param int $times
     * @return array
     */
    private function multiple(string $entityClass, int $times): array
    {
        $entities = $this->factory->haveMultiple($entityClass, $times);
        $this->createdEntities = array_merge($this->createdEntities, $entities);
        return $entities;
    }

    /**
     * Get all created detached entities, find it databse, and remove managed
     *
     * @param \Codeception\TestCase $test
     * @throws \Codeception\Exception\ModuleException
     */
    public function _after(\Codeception\TestCase $test)
    {
        $entities = $this->createdEntities;
        if (!$entities) {
            return;
        }

        $em = $this->getModule('Doctrine2')->_getEntityManager();

        foreach($entities as $entity){
            $managed = $em->getRepository($entity::class)->findOneById($entity->getId());

            if ($managed) {
                $em->remove($managed);
                $em->flush();
            }
        }
    }

    /**
     * Remove customers from database by emails
     *
     * @param array $emails
     * @throws \Codeception\Exception\ModuleException
     */
    public function removeCustomersByEmails(array $emails): void
    {
        $em = $this->getModule('Doctrine2')->_getEntityManager();

        foreach($emails as $email){
            $managed = $em->getRepository(Customers::class)->findOneByEmail($email);

            if ($managed) {
                $em->remove($managed);
                $em->flush();
            }
        }
    }

    /**
     * Return one customer from databse by email
     *
     * @param string $email
     * @return Customers
     */
    public function getCustomerByEmail(string $email): Customers
    {
        $em = $this->getModule('Doctrine2')->_getEntityManager();
        return $em->getRepository(Customers::class)->findOneByEmail($email);
    }
}
