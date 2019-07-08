<?php
declare(strict_types=1);

namespace App\EventListener\Intercom;

use App\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Обработчик события создания меню
 * Class ConfigureMenuListener
 * @package App\EventListener\Intercom
 */
class ConfigureMenuListener
{
    /**
     * Добавление в меню пунктов для этого бандла
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $menu->addChild('Intercoms', ['route' => 'intercom_index'])
            ->setAttribute('class', 'dropdown')
            ->setLinkAttribute('data-toggle', 'dropdown')
            ->setLinkAttribute('class', 'dropdown-toggle nav-link')
            ->setChildrenAttribute('class', 'dropdown-menu bg-nav-dropdown m-1')
            ->setChildrenAttribute('role', 'menu')
            ->setExtra('orderNumber', 3)
        ;
        $menu['Intercoms']->addChild('List', ['route' => 'intercom_index'])
            ->setAttribute('class', 'nav-item pl-3')
            ->setLinkAttribute('class', 'nav-link')
        ;
        $menu['Intercoms']->addChild('Add', ['route' => 'intercom_add'])
            ->setAttribute('class', 'nav-item pl-3')
            ->setLinkAttribute('class', 'nav-link')
        ;
    }
}
