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
            $menu->addChild('Orders', ['route' => 'order'])
                ->setAttribute('icon', 'fas fa-tasks')
                ->setExtra('dropdown', true)
                ->setExtra('orderNumber', 2)
            ;
            $menu['Orders']->addChild('List', ['route' => 'order']);
            $menu['Orders']->addChild('Add', ['route' => 'order.add']);
        }
    }
}
