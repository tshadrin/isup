<?php
declare(strict_types=1);

namespace App\Service\Bitrix\Calendar;

use DateTimeImmutable;
use Exception;
use om\IcalParser;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


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
    /** @var CacheInterface  */
    private $redis;


    /**
     * BitirixCalService constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters, CacheInterface $redis)
    {
        $this->parameters = $parameters;
        $this->redis = $redis;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getActualEvents(): array
    {
        $calendar = $this->getCalendar();

        return $this->redis->get("calendar", function (ItemInterface $item) use ($calendar) {
            $item->expiresAfter(600);

            $events = [];
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
        });
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
