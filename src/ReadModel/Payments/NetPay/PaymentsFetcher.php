<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\NetPay;


use App\ReadModel\Payments\NetPay\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentsFetcher
{

    const RECORDS_LIMIT = 5000;

    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * PaymentsFetcher constructor.
     * @param Connection $connection
     */
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
            ->select('p.user_id', 'p.created', 'p.status', 'p.sum', 'p.updated', 'n.error' , 'nec.description as error_description')
            ->from('netpay', 'p')
            ->leftJoin('n', 'netpay_error_codes', 'nec', 'nec.code = n.error')
            ->leftJoin('p', 'nperrors', 'n', 'p.id = n.id');

        if ($filter->userId) {
            $query->andWhere("p.user_id = :user_id")
                ->setParameter(':user_id', $filter->userId);
        }

        if (isset($filter->status)) {
            if ($filter->status === Payment::STATUS_ERROR) {
                $query->andWhere("n.error is not NULL");
            } else {
                $query->andWhere("p.status = :status")
                    ->setParameter(':status', $filter->status);
            }
        }

        if ($filter->interval) {
            [$from, $to] = $filter->interval;
            $query->where("p.created > :created_from")
                ->andWhere("p.created < :created_to")
                ->setParameter("created_from", $from->format('Y-m-d H:i:s'))
                ->setParameter("created_to", $to->format('Y-m-d H:i:s'));
        }

        $result = $query->orderBy('p.created', 'DESC')
            ->setMaxResults(self::RECORDS_LIMIT)
            ->execute();

        if (!$result->rowCount()) {
            throw new \DomainException($this->translator->trans('Records not found'));
        }

        $payments = $result->fetchAll(FetchMode::CUSTOM_OBJECT, Payment::class);
        return $payments;
    }
}
