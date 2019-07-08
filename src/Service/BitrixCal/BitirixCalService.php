<?php
declare(strict_types=1);

namespace App\Service\BitrixCal;

use om\IcalParser;

/**
 * Парсер календаря в битрикс
 * Class BitirixCalService
 * @package App\Service\BitrixCal
 */
class BitirixCalService
{
    /**
     * Параметры конфигурации сервиса
     * @var array
     */
    private $parameters;

    /**
     * BitrixCalService constructor.
     * @param $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Проверка актуальных событий в календаре
     * @return array
     */
    public function getActualCallEvents(): array
    {
        $cal = new IcalParser();
        $events = [];
        try {
            $cal->parseFile($this->parameters['path']);
            $current_date = new \DateTimeImmutable();
            foreach ($cal->getSortedEvents() as $event) {
                if ($current_date > $event['DTSTART'] && $current_date < $event['DTEND']) {
                    $events[] = ['title' => $event['SUMMARY'], 'description' => $event['DESCRIPTION'],];
                }
            }
        } catch (\Exception $e) {
            return ['error' => 'Error retrieving calendar. '. $e->getMessage()];
        }
        if (0 === count($events)) {
            return ['events' => ['events_count' => count($events),],];
        }
        return ['events' => ['events_count' => count($events), $events,],];
    }
}
