<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\Qiwi;

use App\ReadModel\Payments\Qiwi\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentsFetcher
{

    const RECORDS_LIMIT = 5000;

    /** @var Connection  */
    private $connection;
    /** @var TranslatorInterface  */
    private $translator;

    public function __construct(Connection $connection, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->translator = $translator;
    }

    public function getFilteredPayments(Filter $filter): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('p.account as login', 'p.command', 'p.sum', 'p.txn_date as payDate', 'p.txn_id as transaction',
                'p.request_date as requestDate', 'q.status_pay as processed', 'q.status_fisk as fisk', 'p.user_id as id')
            ->from('qiwi_payments', 'p')
            ->leftJoin('p', 'queue', 'q', 'p.txn_id = q.transaction_id')
            ->where("q.type='qw'");

        if ($filter->userId) {
            $query->andWhere("p.user_id = :user_id")
                ->setParameter(':user_id', $filter->userId);
        }
        if (isset($filter->processed)) {
            $query->andWhere("q.status_pay = :status")
                ->setParameter(':status', $filter->processed);
        }
        if (isset($filter->command)) {
            $query->andWhere("p.command = :command")
                ->setParameter(':command', $filter->command);
        }

        if ($filter->interval) {
            [$from, $to] = $filter->interval;
            $query->andWhere("p.request_date > :created_from")
                ->andWhere("p.request_date < :created_to")
                ->setParameter("created_from", $from->format('Y-m-d H:i:s'))
                ->setParameter("created_to", $to->format('Y-m-d H:i:s'));
        }

        $result = $query->orderBy('p.request_date', 'DESC')
            ->setMaxResults(self::RECORDS_LIMIT)
            ->execute();

        if (!$result->rowCount()) {
            throw new \DomainException($this->translator->trans('Records not found'));
        }

        $payments = $result->fetchAll(FetchMode::CUSTOM_OBJECT, Payment::class);
        return $payments;
    }
}
