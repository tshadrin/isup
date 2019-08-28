<?php
declare(strict_types = 1);


namespace App\Service\Bitrix\Calendar;


class CachedCalendar extends Calendar
{
    const EVENTS_NOT_FOUND = 0;
    const CACHE_LIVETIME = 100;

    /**
     * @var \Redis
     */
    private $redis;

    public function __construct(array $parameters, \Redis $redis)
    {
        $this->redis = $redis;
        parent::__construct($parameters);
    }


    public function getActualEvents(): array
    {
        if ($this->isCacheExists()) {
            return
                $this->isCachedEventsExists() ?
                    json_decode($this->redis->get('events'), true):
                    [];
        }
        $events = parent::getActualEvents();
        $this->cacheEvents($events);
        return $events;
    }

    private function isCachedEventsExists(): bool
    {
        return (int)$this->redis->get('events_count') > self::EVENTS_NOT_FOUND;
    }

    private function isCacheExists(): bool
    {
        return (bool)$this->redis->exists('events_count');
    }

    private function cacheEvents(array $events): void
    {
        $this->cache('events_count', $count = count($events));

        if ($this->isEventsExists($events)) {
            $this->cache('events', json_encode($events));
        }
    }

    private function cache($key, $value): void
    {
        $this->redis->set($key, $value, self::CACHE_LIVETIME);
    }

    private function isEventsExists(array $events): bool
    {
        return count($events) > self::EVENTS_NOT_FOUND;
    }
}
