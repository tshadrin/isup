<?php

namespace App\Repository\SberbankReport;

use Doctrine\ORM\EntityRepository;
use App\SberbankEntity\Payment;

/**
 * Class PaymentRepository
 * @package App\Repository\SberbankReport
 */
class PaymentRepository extends EntityRepository
{
    /**
     * Поиск платежей по интервалу времени
     * @param Payment $payment
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByDateRange(Payment $payment)
    {
        $interval = $payment->getRegDateInterval();
        $query = $this->createQueryBuilder("p");
        $query->where("p.reg_date > :start_interval")
            ->andWhere("p.reg_date < :end_interval")
        ->setParameter("start_interval", $interval[0]->format("Y-m-d H:i:s"))
        ->setParameter("end_interval", $interval[1]->format("Y-m-d H:i:s"));
        if(!empty($payment->getAccountId()))
            $query->andWhere("p.account_id = :account_id")->setParameter('account_id', $payment->getAccountId());
        if(!empty($payment->getPayNum()))
            $query->andWhere("p.pay_num = :pay_num")->setParameter('pay_num', $payment->getPayNum());
        $query->orderBy('p.payment_id', 'DESC');
        return $query;
    }

}
