<?php


namespace App\Service\Bot\Commutator;


interface SwPageGetterInterface
{
    public function getSwPage(string $ip): string;
}