<?php
declare(strict_types=1);

namespace App\EventListener\Order;

use App\Service\Order\OrderService;
use App\Event\UTM5UserFoundEvent;
use Twig\Environment;

/**
 * Class UTM5UserFoundListener
 * @package App\EventListener\Order
 */
class UTM5UserFoundListener
{
    /*
     * var Environment $templating
     */
    private $templating;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * UTM5UserFoundListener constructor.
     * @param Environment $templating
     */
    public function __construct(Environment $templating, OrderService $orderService)
    {
        $this->templating = $templating;
        $this->orderService = $orderService;
    }

    /**
     * @param UTM5UserFoundEvent $event
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function onUTM5UserFound(UTM5UserFoundEvent $event): void
    {
        $orders = $this->orderService->getLastOrders($event->getUser());
        $last_orders = $this->templating->render('Order/last-orders.html.twig', ['orders' => $orders]);
        $event->addResult('last_orders', $last_orders);
    }
}
