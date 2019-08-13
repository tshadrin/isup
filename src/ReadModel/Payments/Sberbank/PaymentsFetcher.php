<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\Sberbank;


use App\ReadModel\Payments\Sberbank\Filter\Filter;
use Doctrine\DBAL\{ Connection, FetchMode };
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentsFetcher
{
    const RECORDS_LIMIT = 2000;
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(Connection $connection, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->translator = $translator;
    }

    /**
     * @param Filter $filter
     * @return array
     */
    public function getFilteredPayments(Filter $filter): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('p.user_id', 'p.amount', 'p.transaction_id as transaction', 'p.pay_date as payDate', 'p.created', 'q.status_pay as processed', 'q.status_fisk as fisk')
            ->from('sber_payments', 'p')
            ->leftJoin('p', 'queue', 'q', 'q.transaction_id = p.transaction_id')
            ->where("q.type = 'sb'");

        if ($filter->userId) {
            $query->andWhere("p.account_id = :user_id")
                ->setParameter(':user_id', $filter->userId);
        }

        if ($filter->transaction) {
            $query->andWhere("p.pay_num = :transaction")
                ->setParameter(':transaction', $filter->transaction);
        }

        if ($filter->interval) {
            [$from, $to] = $filter->interval;
            $query->andWhere("p.created > :created_from")
                ->andWhere("p.created < :created_to")
                ->setParameter("created_from", $from->format('Y-m-d H:i:s'))
                ->setParameter("created_to", $to->format('Y-m-d H:i:s'));
        }

        $result = $query->orderBy('created', 'DESC')
            ->setMaxResults(self::RECORDS_LIMIT)
            ->execute();

        if (!$result->rowCount()) {
            throw new \DomainException($this->translator->trans('Records not found'));
        }


        $payments = $result->fetchAll(FetchMode::CUSTOM_OBJECT, Payment::class);

        return $payments;
    }
}
