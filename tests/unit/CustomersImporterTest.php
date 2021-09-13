<?php


class CustomersImporterTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \App\Serializer\CustomersDenormalizerInterface
     */
    private $denormalizer;

    /**
     * @var \App\Repository\CustomersRepository
     */
    private $repository;
    
    protected function _before()
    {
        $this->denormalizer = new \App\Serializer\CustomersFromRandomUserDenormalizer(
            new \Symfony\Component\Serializer\Normalizer\ObjectNormalizer()
        );

        $this->repository = $this->getMockBuilder(\App\Repository\CustomersRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository
            ->method('saveCustomerBatch')
            ->willReturn($this->returnValue(null));

    }

    /**
     * Test api return new customer result (insert)
     */
    public function testInsertOne()
    {
        $apiProvider = $this->tester->getApiProvider();

        $importer = new \App\Service\CustomersImporter(
            $apiProvider,
            $this->denormalizer,
            $this->repository
        );

        $this->assertEquals(
            [
                'inserted' => 1,
                'updated' => 0,
            ],
            $importer->import()
        );
    }

    /**
     * Test api return result with already exists email (update)
     */
    public function testUpdateOne()
    {
        $apiProvider = $this->tester->getApiProvider();

        $customersArray = $this->tester->getCustomersArray();

        $this->repository
            ->method('getCustomersByEmails')
            ->willReturn([$customersArray[0]['email'] => new \App\Entity\Customers()]);

        $importer = new \App\Service\CustomersImporter(
            $apiProvider,
            $this->denormalizer,
            $this->repository
        );

        $this->assertEquals(
            [
                'inserted' => 0,
                'updated' => 1,
            ],
            $importer->import()
        );
    }

    /**
     * Test empty api result
     */
    public function testInsertAndUpdateNothing()
    {
        // return empty api result (loadByNationality)
        $apiProvider = $this->tester->getApiProvider(true);

        $importer = new \App\Service\CustomersImporter(
            $apiProvider,
            $this->denormalizer,
            $this->repository
        );

        $this->assertEquals(
            [
                'inserted' => 0,
                'updated' => 0,
            ],
            $importer->import()
        );
    }



}