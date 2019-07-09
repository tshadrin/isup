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
     * @var \Redis
     */
    private $redis;

    /**
     * BitrixCalService constructor.
     * @param $parameters
     */
    public function __construct(array $parameters, \Redis $redis)
    {
        $this->parameters = $parameters;
        $this->redis = $redis;
    }

    /**
     * Проверка актуальных событий в календаре
     * @return array
     */
    public function getActualCallEvents(): array
    {
        if($this->redis->exists('events_count')) {
            if(0 === (int)$this->redis->get('events_count')) {
                $events = [];
            } else {
                $events = json_decode($this->redis->get('events'), true);
            }
        } else {
            $events = $this->getEvents();
            $this->redis->set('events_count', count($events), 100);
            $this->redis->set('events', json_encode($events),100);
        }
        if (0 === count($events)) {
            return $data = ['events' => ['events_count' => count($events),],];
        }
        return ['events' => ['events_count' => count($events), $events,],];
    }

    /**
     * Выборка событий календаря из iCall битрикса
     * @return array
     */
    private function getEvents(): array
    {
        $events = [];
        $cal = new IcalParser();
        try {
            $cal->parseFile($this->parameters['path']);
            $current_date = new \DateTimeImmutable();
            foreach ($cal->getSortedEvents() as $event) {
                if ($current_date > $event['DTSTART'] && $current_date < $event['DTEND']) {
                    $events[] = ['title' => $event['SUMMARY'], 'description' => $event['DESCRIPTION'],];
                }
            }
        } catch (\Exception $e) {
            return ['error' => 'Error retrieving calendar. ' . $e->getMessage()];
        }
        return $events;
    }
}
