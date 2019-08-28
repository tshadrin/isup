<?php
declare(strict_types=1);

namespace App\Service\Bot\Commutator;


interface SwPageGetterInterface
{
    public function getSwPage(string $ip): string;
}