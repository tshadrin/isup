<?php

namespace App\Service\SberbankReport;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\SberbankEntity\Payment;


class SberbankReportService
{
    /**
     * Записей на странице
     */
    const ITEMS_PER_PAGE = 30;

    /**
     * Количество вывдимых последних платежей
     */
    const LAST_ITEMS_COUNT = 300;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * SberbankReportManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface $paginator
     */
    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    /**
     * Поиск платежей за период времени с постраничным выводом
     * @param Payment $payment параметры поиска платежа
     * @param int $page номер страницы
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getPaymentsByDateRange(Payment $payment, $page)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $payments = $this->entityManager
            ->getRepository('AppSberbank:Payment')
            ->findByDateRange($payment)
            ->getQuery()->getResult();
        return $this->paginator->paginate($payments, $page, self::ITEMS_PER_PAGE, ['entity' => Payment::class]);
    }

    /**
     * Поиск последних платежей с постраничным выводом
     * @param int $page номер страницы
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getLastPayments($page)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $payments = $this->entityManager
            ->getRepository('AppSberbank:Payment')
            ->createQueryBuilder('p')
            ->orderBy('p.payment_id', 'DESC')
            ->setMaxResults(self::LAST_ITEMS_COUNT)
            ->getQuery()->getResult();
        return $this->paginator->paginate($payments, $page, self::ITEMS_PER_PAGE, ['entity' => Payment::class]);
    }

    /**
     * Поиск записей в логеах по номеру платежа
     * @param int $pay_num номер транзакции
     * @return array
     */
    public function getPaymentLog($pay_num)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->entityManager->getRepository('AppSberbank:PaymentLog')->findByPayNum($pay_num);
    }

    /**
     * Поиск количества записей о платеже в логе
     * @param int $pay_num номе транзакции
     * @return mixed
     */
    public function getCountPaymentLogs($pay_num)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->entityManager->getRepository('AppSberbank:PaymentLog')->getCountPaymentLogRows($pay_num);
    }
}
