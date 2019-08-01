<?php
declare(strict_types = 1);


namespace App\Service\NetPay\ListPayments;


use App\ReadModel\Payments\NetPay\PaymentsFetcher;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\Phone\RowsForm;

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

    public function __construct(PaymentsFetcher $paymentsFetcher, PaginatorInterface $paginator)
    {
        $this->paymentsFetcher = $paymentsFetcher;
        $this->paginator = $paginator;
    }

    /**
     * @param Command $command
     * @return PaginationInterface
     */
    public function handle(Command $command): PaginationInterface
    {
        $payments = $this->paymentsFetcher->getFilteredPayments($command->filter);

       if ($command->rowsOnPage === RowsForm::ALL_ROWS_ON_PAGE) {
            $command->rowsOnPage = count($payments);
       }

       $payments = $this->paginator->paginate($payments, $command->page, $command->rowsOnPage);
       return $payments;
    }
}
