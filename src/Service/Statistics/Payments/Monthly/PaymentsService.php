<?php
declare(strict_types=1);

namespace App\Service\Statistics\Payments\Monthly;


use App\ReadModel\Statistics\MonthlyPaymentsFetcher;
use App\Service\Cache\CacherInterface;

class PaymentsService
{
    /** @var MonthlyPaymentsFetcher  */
    private $monthlyPaymentsFetcher;
    /** @var \Redis  */
    private $redis;

    public function __construct(MonthlyPaymentsFetcher $monthlyPaymentsFetcher, CacherInterface $redis)
    {
        $this->monthlyPaymentsFetcher = $monthlyPaymentsFetcher;
        $this->redis = $redis;
    }

    public function getMonthlyForLastYearGraphData(): array
    {
        return $this->redis->cacheArray('payment_reports', function() {
            $payments = $this->monthlyPaymentsFetcher->getCountByServerForLastYearMonthly();
            return $this->formatDataToGraph($payments);
        });
    }

    /**
     * Форматирует данные для графиков
     */
    private function formatDataToGraph(array $payments): array
    {
        $graphData = [];
        foreach ($payments as $month => $data) {
            $graphData[$month] = ['counts' => [], 'sums' => [], 'servers' => [],];
            foreach ($data as $values) {
                $graphData[$month]['counts'][] = $values['count'];
                $graphData[$month]['sums'][] = $values['sum'];
                $graphData[$month]['servers'][] = $values['server'];
            }
            $graphData[$month]['counts'] = implode(", ", $graphData[$month]['counts']);
            $graphData[$month]['sums'] = implode(", ", $graphData[$month]['sums']);
            $graphData[$month]['servers'] = implode(", ", $graphData[$month]['servers']);
        }
        return $graphData;
    }
}