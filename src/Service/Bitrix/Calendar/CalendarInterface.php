<?php
declare(strict_types=1);

namespace App\Service\Bitrix\Calendar;


interface CalendarInterface
{
    public function getActualEvents(): array;
}