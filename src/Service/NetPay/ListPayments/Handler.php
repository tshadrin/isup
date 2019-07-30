<?php
declare(strict_types = 1);


namespace App\Service\NetPay\ListPayments;


use App\ReadModel\Payments\NetPay\Payment;
use App\ReadModel\Payments\NetPay\PaymentsFetcher;
use App\ReadModel\Payments\NetPay\UserFetcher;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\Phone\RowsForm;

class Handler
{
    public const PAGINATION_NAME = 'netpay';
    /**
     * @var PaymentsFetcher
     */
    private $paymentsFetcher;
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    /**
     * @var UserFetcher
     */
    private $userFetcher;

    public function __construct(PaymentsFetcher $paymentsFetcher, PaginatorInterface $paginator, UserFetcher $userFetcher)
    {
        $this->paymentsFetcher = $paymentsFetcher;
        $this->paginator = $paginator;
        $this->userFetcher = $userFetcher;
    }

    /**
     * @param Command $command
     * @return PaginationInterface
     */
    public function handle(Command $command): PaginationInterface
    {
        /** @var Payment[] $payments */
        $payments = $this->paymentsFetcher->getFilteredPayments($command->filter);

       if ($command->rowsOnPage === RowsForm::ALL_ROWS_ON_PAGE) {
            $command->rowsOnPage = count($payments);
       }

       $payments = $this->paginator->paginate($payments, $command->page, $command->rowsOnPage, ['name' => self::PAGINATION_NAME]);
       return $payments;
    }
}
