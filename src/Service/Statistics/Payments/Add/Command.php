<?php
declare(strict_types=1);

namespace App\Service\Statistics\Payments\Add;

use Webmozart\Assert\Assert;

class Command
{
    const MINIMAL_YEAR = "2000";
    const FIRST_MONTH_NUMBER = 1;
    const LAST_MONTH_NUMBER = 12;
    /**
     * @var int
     */
    public $month;
    /**
     * @var int
     */
    public $year;

    public function __construct(int $month, int $year)
    {
        Assert::greaterThanEq($year, self::MINIMAL_YEAR);
        Assert::lessThanEq($year, $this->getCurrentYear());

        Assert::greaterThanEq($month, self::FIRST_MONTH_NUMBER);
        if($this->isCurrentYear($year)) {
            Assert::lessThan($month, $this->getCurrentMonth());
        } else {
            Assert::lessThanEq($month, self::LAST_MONTH_NUMBER);
        }

        $this->month = $month;
        $this->year = $year;
    }

    private function isCurrentYear(int $year): bool
    {
        return $year === $this->getCurrentYear();
    }

    private function getCurrentYear(): int
    {
        return (int)(new \DateTimeImmutable())->format("Y");
    }

    private function getCurrentMonth(): int
    {
        return (int)(new \DateTimeImmutable())->format("m");
    }
}