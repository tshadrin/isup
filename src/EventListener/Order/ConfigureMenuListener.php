<?php
declare(strict_types=1);

namespace App\EventListener\Order;

use App\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ConfigureMenuListener
 * @package App\EventListener\Order
 */
class ConfigureMenuListener
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * ConfigureMenuListener constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Добавление в меню пунктов для этого бандла
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        if($this->authorizationChecker->isGranted('ROLE_SUPPORT')) {
            $menu = $event->getMenu();
            $menu->addChild('Orders', ['route' => 'orders_index'])
                ->setAttribute('class', 'dropdown')
                ->setLinkAttribute('data-toggle', 'dropdown')
                ->setLinkAttribute('class', 'dropdown-toggle nav-link')
                ->setChildrenAttribute('class', 'dropdown-menu bg-nav-dropdown m-1')
                ->setChildrenAttribute('role', 'menu')
                ->setExtra('orderNumber', 2)
                ->setExtra('routes', [
                    ['route' =>'order_'],
                    ['pattern' => '/^order.+/'],
                ])
            ;
            $menu['Orders']->addChild('List', ['route' => 'orders_index'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link')
            ;
            $menu['Orders']->addChild('Add', ['route' => 'order_add'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link')
            ;
        }
    }
}
