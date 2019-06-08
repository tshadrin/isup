<?php

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
    public function onMenuConfigure(ConfigureMenuEvent $event)
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
            ->setLinkAttribute('class', 'nav-link');
        $menu['Intercoms']->addChild('Add', ['route' => 'intercom_add'])
            ->setAttribute('class', 'nav-item pl-3')
            ->setLinkAttribute('class', 'nav-link');
    }
}
