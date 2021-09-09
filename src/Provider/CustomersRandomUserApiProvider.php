<?php
declare(strict_types=1);

namespace App\Provider;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CustomersRandomUserApiProvider implements CustomersProviderInterface {

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $emails = [];

    /**
     * CustomersApiProvider constructor.
     *
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * Set provider configuration
     *
     * @param array $config
     */
    public function setParams(array $params = []): void
    {
        $this->params = $params;
    }

    /**
     * Set request base url
     *
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Load users from remote API filtering by nationality
     *
     * @param string $nationality
     * @throws \Throwable
     */
    public function loadByNationality(
        string $nationality = CustomersProviderInterface::NATIONALITY_AU,
        int $count = 100): \Generator
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl, [
                'query' => array_merge(
                    $this->params,
                    [
                        'nat'     => $nationality,
                        'results' => $count,
                    ]
                ),
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $customersArray = json_decode($response->getContent(), true);

            if (isset($customersArray['results']) && is_array($customersArray['results'])) {
                // prepare raw emails array (for later optimization)
                $this->emails = array_column($customersArray['results'], 'email');
                // return array of customers
                yield $customersArray['results'];
            }

        } catch(\Throwable $exception) {
            $this->logger->error('RandomUserAPI error', [
                'exception' => $exception,
            ]);

            throw $exception;
        }

    }

    /**
     * Return array of customers fetched from API
     *
     * @return array
     */
    public function getLoadedEmails(): array
    {
        return $this->emails;
    }


}