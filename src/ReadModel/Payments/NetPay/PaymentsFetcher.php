<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\NetPay;


use App\ReadModel\Payments\NetPay\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class PaymentsFetcher
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    const RECORDS_LIMIT = 5000;

    /**
     * @param Filter $filter
     * @return array
     */
    public function getFilteredPayments(Filter $filter): array
    {
            $query = $this->connection->createQueryBuilder()
                ->select('p.user_id', 'p.created', 'p.status', 'p.sum', 'p.updated', 'n.error')
                ->from('netpay', 'p')
                ->leftJoin('p', 'nperrors', 'n', 'p.id = n.id');
            if ($filter->interval) {
                [$from, $to] = $filter->interval;
                $query->where("p.created > :created_from")
                    ->andWhere("p.created < :created_to")
                    ->setParameter("created_from", $from->format('Y-m-d H:i:s'))
                    ->setParameter("created_to", $to->format('Y-m-d H:i:s'));
            }
            if (!is_null($filter->status)) {
                if ($filter->status === Payment::STATUS_ERROR) {
                    $query->andWhere("n.error is not NULL");
                } else {
                    $query->andWhere("p.status = :status")
                        ->setParameter(':status', $filter->status);
                }
            }
            if($filter->userId) {
                $query->andWhere("p.user_id = :user_id")
                    ->setParameter(':user_id', $filter->userId);
            }

            $result = $query->orderBy('p.created', 'DESC')
                ->setMaxResults(self::RECORDS_LIMIT)
                ->execute();
            $payments = $result->fetchAll(FetchMode::CUSTOM_OBJECT, Payment::class);
        return $payments;
    }
}