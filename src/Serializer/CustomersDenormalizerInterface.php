<?php
declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

interface CustomersDenormalizerInterface extends ContextAwareDenormalizerInterface {

    public function getEmail(array $rawCustomerData): string;

}