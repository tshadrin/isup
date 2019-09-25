<?php

namespace App\Mapper\UTM5;

use App\Collection\UTM5\PaymentCollection;
use App\Entity\UTM5\Payment;
use Doctrine\DBAL\{ Connection, DBALException };
use Doctrine\DBAL\Driver\Statement;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentMapper
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(Connection $UTM5Connection, TranslatorInterface $translator)
    {
        $this->connection = $UTM5Connection;
        $this->translator = $translator;
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getLastPaymentsStmt(): Statement
    {
        $sql = "(SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM UTM5.payment_transactions p
				INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2019 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2018_2 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2018 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account)
                ORDER BY payment_date DESC
                LIMIT 10";
        return $this->connection->prepare($sql);
    }

    /**
     * Получение последних платежей пользователя
     * @param int $account
     * @return PaymentCollection|null
     */
    public function getLastPayments(int $account): ?PaymentCollection
    {
        try {
            $stmt = $this->getLastPaymentsStmt();
            $stmt->execute([':basic_account' => $account]);
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $payments = new PaymentCollection();
                foreach($data as $row) {
                    $payment = new Payment($row['amount'], \DateTimeImmutable::createFromFormat("U", $row['payment_date']),
                        (int)$row['transaction_number'], $row['method'], $row['receive'], $row['user_comment']);
                    $payments->add($payment);
                }
                return $payments;
            }
            return null;
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Paymets for user query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getLastPaymentStmt(): Statement
    {
        $sql = "(SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM UTM5.payment_transactions p
				INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2019 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2018_2 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2018 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2016_2 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
               (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2016_1 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2015_2 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2015_1 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
               (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2014 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
               (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2013_1 p
                INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2012_2 p
				INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                UNION
                (SELECT p.payment_absolute AS amount,
                       p.payment_enter_date AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2012_1 p
				INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account AND p.payment_absolute>0 AND p.method <>7)
                ORDER BY payment_date DESC
                LIMIT 1";
        return $this->connection->prepare($sql);
    }

    /**
     * Получение последнего платежа пользователя
     * @param int $account
     * @return Payment|null
     */
    public function getLastPayment(int $account): ?Payment
    {
        try {
            $stmt = $this->getLastPaymentStmt();
            $stmt->execute([':basic_account' => $account]);
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                $payment = new Payment($row['amount'], \DateTimeImmutable::createFromFormat("U", $row['payment_date']),
                    (int)$row['transaction_number'], $row['method'], $row['receive'], $row['user_comment']);
                return $payment;
            }
            return null;
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Paymets for user query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }
}
