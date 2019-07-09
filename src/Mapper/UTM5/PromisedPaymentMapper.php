<?php

namespace App\Mapper\UTM5;

use App\Entity\UTM5\PromisedPayment;
use Doctrine\DBAL\{ Connection, DBALException };
use Doctrine\DBAL\Driver\Statement;
use Symfony\Contracts\Translation\TranslatorInterface;

class PromisedPaymentMapper
{

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
     * @return Statement
     * @throws DBALException
     */
    public function getPromisedPaymentDataStmt(): Statement
    {
        $sql = "SELECT c.expire_date, c.start_date, c.value as amount, c.payment_trans_id as transaction_id
                FROM credits  c
                    JOIN promised_payment_data p
                WHERE c.status=0
                  AND c.is_passed=0
                  AND c.start_date+864000 = c.expire_date
                  AND c.start_date = p.last_payment_date
                  AND c.account_id = :account_id";
        return $this->connection->prepare($sql);
    }

    /**
     * Поиск обещанного платежа
     * @param int $account
     * @return PromisedPayment|null
     */
    public function getPromisedPayment(int $account): ?PromisedPayment
    {
        try {
            $stmt = $this->getPromisedPaymentDataStmt();
            $stmt->execute([':account_id' => $account]);
            if(1 === $stmt->rowCount()) {
                $data = $stmt->fetch(\PDO::FETCH_ASSOC);
                return new PromisedPayment(
                    \DateTimeImmutable::createFromFormat('U', $data['start_date']),
                    \DateTimeImmutable::createFromFormat('U', $data['expire_date']),
                    $data['amount'],
                    $data['transaction_id']
                );
            }
            return null;
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Check user passport query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }
}