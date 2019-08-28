<?php
declare(strict_types=1);

namespace App\Service\SMS;

interface SenderInterface
{
    public function send(string $phone, string $message): void;
}
