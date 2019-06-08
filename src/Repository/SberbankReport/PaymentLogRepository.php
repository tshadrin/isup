<?php

namespace App\Repository\SberbankReport;

use Doctrine\ORM\EntityRepository;

/**
 * Class PaymentLogRepository
 * @package App\Repository\SberbankReport
 */
class PaymentLogRepository extends EntityRepository
{
    /**
     * Поиск записей в логе по номеру транзакции
     * @param $pay_num
     * @return array
     */
    public function findByPayNum($pay_num)
    {
        return $this->createQueryBuilder('p')
            ->where("p.in_data LIKE :pay_num")
            ->setParameter('pay_num', "%pay_num => {$pay_num}%")
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск количества записей в логе по номеру транзакции
     * @param $pay_num
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountPaymentLogRows($pay_num)
    {
        $sql = "SELECT count(l.in_data) FROM logs l WHERE MATCH(l.in_data) AGAINST(:pay_num)";
        $query = $this->getEntityManager()->getConnection()->prepare($sql);
        if($query->execute([':pay_num' => $pay_num])) {
            $result = $query->fetchColumn();
            return (int)$result;
        }
        throw new \DomainException("Not found payment data in log");
    }
}
