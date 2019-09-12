<?php
declare(strict_types = 1);


namespace App\ReadModel;

use DateTimeImmutable;
use Symfony\Component\Form\CallbackTransformer;

class DateIntervalTransformer extends CallbackTransformer
{
    const DATES_DELIMITER = ' - ';

    public static function factory(): self
    {
        return new self(
            static function(?array $interval): ?string
            {
                if (isset($interval)) {
                    return "{$interval[0]->format('d-m-Y')} - {$interval[1]->format('d-m-Y')}";
                }
                return null;
            },
            static function(?string $interval): ?array
            {
                if (isset($interval)) {
                    [$from, $to] = explode(self::DATES_DELIMITER, $interval);
                    return [
                        DateTimeImmutable::createFromFormat("!d-m-Y", $from),
                        DateTimeImmutable::createFromFormat("!d-m-Y", $to)
                    ];
                }
                return null;
            });
    }
}