<?php
declare(strict_types=1);

namespace App\Service\Bitrix\Task\Add;

class PeriodChoices
{
    public static function getInvoiceMonths(): array
    {
        $date = new \DateTimeImmutable();
        $date = $date->setTime(0,0,0);
        $date = $date->setDate((int)$date->format("Y"),1,1);
    }
}