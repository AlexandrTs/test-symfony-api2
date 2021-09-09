<?php

use Mcustiel\Phiremock\Client\Phiremock;
use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Client\Utils\Respond;

class ImportCest
{
    /**
     * Dir with Phiremock response body files
     */
    const EXPECTATIONS_BODY_DIR = '/_data/phiremock-expectations-body';

    /**
     * Expected response body from Phiremock
     *
     * @var string
     */
    private $apiResponseBody;

    /**
     * Parsed to array expected response body
     *
     * @var array
     */
    private $apiResponseCustomers = [];

    /**
     * Add expected response to Phiremock server
     *
     * @param ApiTester $I
     */
    public function _before(ApiTester $I)
    {
        $filesDir = __DIR__ . '/../' . self::EXPECTATIONS_BODY_DIR;
        $fileNum  = mt_rand(1, 5);
        $body     = file_get_contents(sprintf('%s/randomuser_100_%d.json', $filesDir, $fileNum));

        $I->haveACleanSetupInRemoteService();

        $I->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                // real uri is not important here because we need to test only one request
                A::getRequest()->andUrl(Is::matching('~.*~'))
            )->then(
                Respond::withStatusCode(200)->andBody($body)
            )
        );
        // save for later
        $this->apiResponseBody      = $body;
        $this->apiResponseCustomers = json_decode($body, true)['results'];
    }

    /**
     * Clear database
     *
     * @param ApiTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function _after(ApiTester $I)
    {
        if ($this->apiResponseCustomers) {
            $emails = array_column($this->apiResponseCustomers, 'email');
            $I->removeCustomersByEmails($emails);
        }
    }

    /**
     * Make request to API and check imported users count in database
     *
     * @param ApiTester $I
     */
    public function customersImport(ApiTester $I)
    {
        $I->wantTo('Import customers');
        $I->getImporter()->import();

        // get random customer for check in database
        $emails      = array_column($this->apiResponseCustomers, 'email');
        $randomEmail = $emails[mt_rand(0, sizeof($emails)-1)];

        $I->seeInRepository(\App\Entity\Customers::class, ['email' => $randomEmail]);
    }

    /**
     * Check customer update during import
     *
     * Send request to api two times, and change username in api response (for second request)
     * and already existent user name must be updated in database
     *
     * @param ApiTester $I
     */
    public function updateCustomer(ApiTester $I)
    {
        $I->wantTo('Update customer');

        $I->comment('First import');
        $importer = $I->getImporter();
        $importer->import();

        // get expected mock server response (from first iteration)
        $bodyAsArray = json_decode($this->apiResponseBody, true);

        // generate new random name
        $name = [
            'title' => 'Mr',
            'first' => 'John',
            'last'  => 'Doe' . mt_rand(1,11111111111)
        ];

        // get random customer from array (and update it)
        $customerNum = mt_rand(0, sizeof($bodyAsArray['results'])-1);
        $customer = $bodyAsArray['results'][$customerNum];

        // selected customer name
        $customerName = $customer['name']['first'] . ' ' . $customer['name']['last'];

        // update customer in expected response (future response)
        $bodyAsArray['results'][$customerNum]['name'] = $name;

        // new customer name
        $customerNameNew = $name['first'] . ' ' . $name['last'];

        $I->comment('Check new and old customer names are not equal');
        $I->assertNotEquals($customerName, $customerNameNew);

        // grab our test customer from database
        $customerObj = $I->getCustomerByEmail($customer['email']);

        $I->comment('Grab test customer from database and check name');
        $I->assertEquals($customerName, $customerObj->getFullName());

        // serialize changed body to json
        $body = json_encode($bodyAsArray);

        // prepare mock server answer for next request (with new body)
        $I->haveACleanSetupInRemoteService();
        $I->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                // real url is not important here because we need to test only one request
                A::getRequest()->andUrl(Is::matching('~.*~'))
            )->then(
                Respond::withStatusCode(200)->andBody($body)
            )
        );

        $I->comment('Second import (same data)');
        $importer->import();

        // grab our test customer from database
        $customerObj = $I->getCustomerByEmail($customer['email']);

        $I->comment('Check test customer exists in database');
        $I->assertTrue($customerObj instanceof \App\Entity\Customers);

        $I->comment('Check, grabbed from database customer name must be updated');
        $I->assertEquals($customerNameNew, $customerObj->getFullName());

    }

}
