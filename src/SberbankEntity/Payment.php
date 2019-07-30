<?php

namespace App\SberbankEntity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Payment
 * @package App\SberbankEntity
 * @ORM\Entity(repositoryClass="App\Repository\SberbankReport\PaymentRepository")
 * @ORM\Table(name="payments")
 */
class Payment
{
    /**
     * Номер записи в базе
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $payment_id;

    /**
     * Сумма платежа
     * @var double
     * @ORM\Column(type="float", length=255, nullable=true)
     */
    private $amount;

    /**
     * Номер транзакции
     * @var int
     * @ORM\Column(type="integer", length=255)
     * @Assert\Type(type="numeric")
     */
    private $pay_num;

    /**
     * Дата платежа
     * @var int
     * @ORM\Column(type="datetime", length=255)
     */
    private $reg_date;

    /**
     * Идентификатор пользователя
     * @var int
     * @ORM\Column(type="integer", length=255)
     * @Assert\Range(min="1", max="100000")
     */
    private $account_id;

    /**
     * @var array
     * @Assert\Type(type="array")
     * @Assert\NotBlank()
     */
    private $reg_date_interval;

    /**
     * @var int
     */
    private $log_count;

    /**
     * @return int
     */
    public function getPaymentId() { return $this->payment_id; }

    /**
     * @return float
     */
    public function getAmount() { return $this->amount; }

    /**
     * @return int
     */
    public function getPayNum() { return $this->pay_num; }

    /**
     * @return int
     */
    public function getRegDate() { return $this->reg_date; }

    /**
     * @return int
     */
    public function getAccountId() { return $this->account_id; }

    /**
     * @return array
     */
    public function getRegDateInterval() { return $this->reg_date_interval; }

    /**
     * @return int
     */
    public function getLogCount() { return $this->log_count; }
    /**
     * @param $account_id
     * @return $this
     */
    public function setAccountId($account_id) { $this->account_id = $account_id; return $this; }

    /**
     * @param $amount
     * @return $this
     */
    public function setAmount($amount) { $this->amount = $amount; return $this; }

    /**
     * @param $payment_id
     * @return $this
     */
    public function setPaymentId($payment_id) { $this->payment_id = $payment_id; return $this; }

    /**
     * @param $pay_num
     * @return $this
     */
    public function setPayNum($pay_num) { $this->pay_num = $pay_num; return $this; }

    /**
     * @param $reg_date
     * @return $this
     */
    public function setRegDate($reg_date) { $this->reg_date = $reg_date; return $this; }

    /**
     * @param $reg_date_interval
     * @return $this
     */
    public function setRegDateInterval(array $reg_date_interval) { $this->reg_date_interval = $reg_date_interval; return $this; }

    /**
     * @param $log_count
     * @return $this
     */
    public function setLogCount($log_count) { $this->log_count = $log_count; return $this; }
}
