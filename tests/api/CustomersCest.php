<?php

use App\Entity\Customers;

class CustomersCest
{
    /**
     * Get one customer by id
     *
     * @param ApiTester $I
     */
    public function getOne(ApiTester $I)
    {
        $I->wantTo('Get one customer by id');
        $customer = $I->haveCustomer();
        $I->sendGet('/customers/' . $customer->getId());
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['email' => $customer->getEmail()]);
    }

    /**
     * Try to get one not existent customer by id
     *
     * @param ApiTester $I
     */
    public function getOneNegative(ApiTester $I)
    {
        $I->wantTo('Get one not existent customer');
        $I->sendGet('/customers/' . mt_rand(1000000,9000000));
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
    }

    /**
     * Get all customers
     *
     * @param ApiTester $I
     */
    public function getList(ApiTester $I)
    {
        $I->wantTo('Get all customers');
        $num = mt_rand(27, 42);
        $customers = $I->haveCustomers($num);
        $I->sendGet('/customers', );
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();

        $I->comment('Check count in database and in API response');
        $dbCustomersCount  = sizeof($I->grabEntitiesFromRepository(Customers::class));
        $jsonResponse      = json_decode($I->grabResponse(), true);
        $apiCustomersCount = is_array($jsonResponse) ? sizeof($jsonResponse) : 0;
        // I use dev environment. For test database you need to check assertEquals
        $I->assertGreaterOrEquals($dbCustomersCount, $apiCustomersCount);

        $id = $customers[mt_rand(0, $num-1)]->getId();
        $I->comment('Find user with id: ' . $id . ' in json response');
        $I->seeResponseContainsJson(['id' => $id]);
    }
}
