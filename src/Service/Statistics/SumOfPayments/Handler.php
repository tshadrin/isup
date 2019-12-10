<?php
declare(strict_types=1);

namespace App\Service\Statistics\SumOfPayments;

use App\ReadModel\Statistics\SumOfPaymentsFetcher;
use Symfony\Contracts\Cache\CacheInterface;

class Handler
{
    private const REPORT_INTRERVAL = 37;
    /** @var SumOfPaymentsFetcher */
    private $fetcher;
    /** @var CacheInterface */
    private $redis;

    public function __construct(SumOfPaymentsFetcher $fetcher, CacheInterface $redis)
    {
        $this->fetcher = $fetcher;
        $this->redis = $redis;
    }

    public function handle(): array
    {
        $result = [];
        $startMonth = (new \DateTimeImmutable())
            ->setTime(0,0,0)
            ->modify("first day of this month");
        for ($i = 0; $i < self::REPORT_INTRERVAL; $i++) {
            $result[] = [
                'month' => $startMonth->modify("-1 month")->format("m-Y"),
                'count' => $this->redis->get("count-{$startMonth->format("m-Y")}", function() use ($startMonth) {
                    return $this->fetcher->getPaymentsCountByMonths($startMonth);
                }),
                'plus' => $this->redis->get("plus-{$startMonth->format("m-Y")}", function() use ($startMonth) {
                    return $this->fetcher->getPaymentsSumByMonthGreaterNull($startMonth);
                }),
                'minus' => $this->redis->get("minus-{$startMonth->format("m-Y")}", function() use ($startMonth) {
                    return $this->fetcher->getPaymentsSumByMonthLessNull($startMonth);
                }),
                'all' => $this->redis->get("all-{$startMonth->format("m-Y")}", function() use ($startMonth) {
                    return $this->fetcher->getPaymentsSumByMonth($startMonth);
                }),
            ];
            $startMonth = $startMonth->modify("-1 month");
        }
        return $result;
    }
}