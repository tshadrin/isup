<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\Sberbank;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentsLogFetcher
{
    /** @var Connection  */
    private $connection;
    /** @var TranslatorInterface  */
    private $translator;

    public function __construct(Connection $connection, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->translator = $translator;
    }

    public function getByTransaction(int $transaction): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('l.date', 'l.ip', 'l.in_data', 'l.out_data', 'l.err_code', 'l.err_text')
            ->from('log_sber', 'l')
            ->where("l.in_data LIKE :pay_num")
            ->setParameter('pay_num', "%pay_num => {$transaction}%")
            ->execute();

        if(!$result->rowCount()) {
            throw new \DomainException('Logs for payment not found');
        }

        $logRows = $result->fetchAll(FetchMode::CUSTOM_OBJECT, PaymentLog::class);
        return $logRows;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCountPaymentLogRows(int $transaction): int
    {
        $query = "SELECT count(l.in_data)
                  FROM log_sber l
                  WHERE MATCH(l.in_data) AGAINST(:pay_num)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([':pay_num' => $transaction]);

        if(!$stmt->rowCount()) {
            throw new \DomainException("Not found payment data in log");
        }
        return (int)$stmt->fetchColumn();
    }
}