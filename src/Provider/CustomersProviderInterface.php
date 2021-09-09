<?php
declare(strict_types=1);

namespace App\Provider;

interface CustomersProviderInterface {

    const NATIONALITY_AU = 'au';

    public function setParams(array $params): void;

    public function setBaseUrl(string $baseUrl): void;

    public function loadByNationality(string $nationality, int $count = 100): \Generator;

    public function getLoadedEmails(): array;
}