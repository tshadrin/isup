<?php

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
    private $order_service;

    /**
     * UTM5UserFoundListener constructor.
     * @param Environment $templating
     */
    public function __construct(Environment $templating, OrderService $order_service)
    {
        $this->templating = $templating;
        $this->order_service = $order_service;
    }

    /**
     * Обработчик рендерит шаблон для диагностики
     * @param UTM5UserFoundEvent $event
     */
    public function onUTM5UserFound(UTM5UserFoundEvent $event)
    {
        $orders = $this->order_service->getLastOrders($event->getUser());
        $last_orders = $this->templating->render('Order/last-orders.html.twig', ['orders' => $orders]);
        $event->addResult('last_orders', $last_orders);
    }
}
