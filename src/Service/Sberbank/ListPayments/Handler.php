<?php
declare(strict_types = 1);


namespace App\Service\Sberbank\ListPayments;


use App\Form\Phone\RowsForm;
use App\ReadModel\Payments\Sberbank\Payment;
use App\ReadModel\Payments\Sberbank\PaymentsFetcher;
use App\ReadModel\Payments\Sberbank\PaymentsLogFetcher;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class Handler
{
    /**
     * @var PaymentsFetcher
     */
    private $paymentsFetcher;
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    /**
     * @var PaymentsLogFetcher
     */
    private $paymentsLogFetcher;


    public function __construct(PaymentsFetcher $paymentsFetcher, PaginatorInterface $paginator, PaymentsLogFetcher $paymentsLogFetcher)
    {
        $this->paymentsFetcher = $paymentsFetcher;
        $this->paginator = $paginator;
        $this->paymentsLogFetcher = $paymentsLogFetcher;
    }

    public function handle(Command $command): PaginationInterface
    {
        $payments = $this->paymentsFetcher->getFilteredPayments($command->filter);

        if ($command->rowsOnPage === RowsForm::ALL_ROWS_ON_PAGE) {
            $command->rowsOnPage = count($payments);
        }

        $payments = $this->paginator->paginate($payments, $command->page, $command->rowsOnPage);
        foreach ($payments as $payment) {
            $payment->logCount = $this->paymentsLogFetcher->getCountPaymentLogRows($payment->getTransaction());
        }

        return $payments;
    }
}