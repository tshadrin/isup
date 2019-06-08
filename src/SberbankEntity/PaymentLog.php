<?php

namespace App\SberbankEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PaymentLog
 * @package App\SberbankEntity
 * @ORM\Entity(repositoryClass="App\Repository\SberbankReport\PaymentLogRepository")
 * @ORM\Table(name="logs")
 */
class PaymentLog
{
    /**
     * Номер записи в базе
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $log_id;

    /**
     * Дата платежа
     * @var string
     * @ORM\Column(type="datetime", length=255)
     */
    private $date;

    /**
     * ip
     * @var string
     * @ORM\Column(type="text", length=255)
     */
    private $ip;

    /**
     * Номер транзакции
     * @var int
     * @ORM\Column(type="text", length=255)
     */
    private $in_data;

    /**
     * @var int
     * @ORM\Column(type="text", length=255)
     */
    private $out_data;

    /**
     * Номер транзакции
     * @var int
     * @ORM\Column(type="integer", length=255)
     */
    private $err_code;

    /**
     * @var int
     * @ORM\Column(type="text", length=255)
     */
    private $err_text;

    /**
     * @return int
     */
    public function getLogId() { return $this->log_id; }

    /**
     * @return string
     */
    public function getDate() { return $this->date; }

    /**
     * @return float
     */
    public function getIp() { return $this->ip; }

    /**
     * @return int
     */
    public function getInData() { return $this->in_data; }

    /**
     * @return int
     */
    public function getOutData() { return $this->out_data; }

    /**
     * @return int
     */
    public function getErrCode() { return $this->err_code; }

    /**
     * @return int
     */
    public function getErrText() { return $this->err_text; }

    /**
     * @param $log_id
     * @return $this
     */
    public function setLogId($log_id) { $this->log_id = $log_id; return $this; }

    /**
     * @param $date
     * @return $this
     */
    public function setDate($date) { $this->date = $date; return $this; }

    /**
     * @param $ip
     * @return $this
     */
    public function setIp($ip) { $this->ip = $ip; return $this; }

    /**
     * @param $in_data
     * @return $this
     */
    public function setInData($in_data) { $this->in_data = $in_data; return $this; }

    /**
     * @param $out_data
     * @return $this
     */
    public function setOutData($out_data) { $this->out_data = $out_data; return $this; }

    /**
     * @param $err_code
     * @return $this
     */
    public function setErrCode($err_code) { $this->err_code = $err_code; return $this; }

    /**
     * @param $err_text
     * @return $this
     */
    public function setErrText($err_text) { $this->err_text = $err_text; return $this; }
}
