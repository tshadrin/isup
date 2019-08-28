<?php
declare(strict_types=1);

namespace App\Service\Order\ShowList;


use App\Form\RowsForm;
use App\ReadModel\Orders\ShowList\Order;
use App\ReadModel\Orders\ShowList\OrdersFetcher;
use App\Repository\UTM5\PassportRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class Handler
{
    /**
     * @var OrdersFetcher
     */
    private $ordersFetcher;
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    /**
     * @var PassportRepository
     */
    private $passportRepository;

    public function __construct(OrdersFetcher $ordersFetcher,
                                PaginatorInterface $paginator,
                                PassportRepository $passportRepository)
    {
        $this->ordersFetcher = $ordersFetcher;
        $this->paginator = $paginator;
        $this->passportRepository = $passportRepository;
    }

    public function handle(Command $command): PaginationInterface
    {
        $orders = $this->ordersFetcher->getFilteredOrders($command->filter);

        if ($command->rowsOnPage === RowsForm::ALL_ROWS_ON_PAGE) {
            $command->rowsOnPage = count($orders) === 0 ? 1: count($orders);
        }

        $orders = $this->paginator->paginate($orders, $command->page, $command->rowsOnPage);
        $this->checkPassportData($orders);
        return $orders;
    }

    /**
     * Ставит состояние поля emptyPassport = true если паспортные данные не заполнены
     * @param PaginationInterface $orders
     */
    private function checkPassportData(PaginationInterface $orders): void
    {
        /** @var Order[] $orders */
        foreach ($orders as $order) {
            if ($order->isUtm5Order()) {
                $passport = $this->passportRepository->getById((int)$order->utm_id);
                $order->emptyPassport = $passport->isNotFill();
            }
        }
    }
}
