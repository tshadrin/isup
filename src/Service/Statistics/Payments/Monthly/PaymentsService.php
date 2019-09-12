<?php
declare(strict_types=1);

namespace App\Service\Statistics\Payments\Monthly;


use App\ReadModel\Statistics\MonthlyPaymentsFetcher;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PaymentsService
{
    /** @var MonthlyPaymentsFetcher  */
    private $monthlyPaymentsFetcher;
    /** @var \Redis  */
    private $pdo;

    public function __construct(MonthlyPaymentsFetcher $monthlyPaymentsFetcher, CacheInterface $pdo)
    {
        $this->monthlyPaymentsFetcher = $monthlyPaymentsFetcher;
        $this->pdo = $pdo;
    }

    public function getMonthlyForLastYearGraphData(): array
    {
        return $this->pdo->get('payment_reports', function(ItemInterface $item) {
            $item->expiresAfter(600);

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