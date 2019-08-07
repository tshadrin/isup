<?php
declare(strict_types=1);

namespace App\Service\Bitrix\Calendar;

use DateTimeImmutable;
use Exception;
use om\IcalParser;



/**
 * Парсер календаря в битрикс
 * Class BitirixCalService
 * @package App\Service\BitrixCal
 */
class Calendar implements CalendarInterface
{
    /**
     * Параметры конфигурации сервиса
     * @var array
     */
    private $parameters;


    /**
     * BitirixCalService constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getActualEvents(): array
    {
        $events = [];
        $calendar = $this->getCalendar();
        $currentDate = new DateTimeImmutable();

        foreach ($calendar->getSortedEvents() as $event) {
            if ($currentDate > $event['DTSTART'] &&
                $currentDate < $event['DTEND']) {
                $events[] = [
                    'title' => $event['SUMMARY'],
                    'description' => $event['DESCRIPTION'],
                    ];
            }
        }

        return $events;
    }

    /**
     * @return IcalParser
     * @throws Exception
     */
    private function getCalendar(): IcalParser
    {
        $calendar = new IcalParser();
        $calendar->parseFile($this->parameters['path']);

        return $calendar;
    }
}
