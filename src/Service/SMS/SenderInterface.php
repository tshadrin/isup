<?php

namespace App\Service\SMS;

interface SenderInterface
{
    public function send(string $phone, string $message): void;
}
