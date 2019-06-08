<?php
namespace App\EventListener\UTM5;

use App\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Обработчик события создания меню
 * Class ConfigureMenuListener
 * @package App\EventListener\UTM5
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
        $menu->addChild('Search in UTM5', ['route' => 'search_default'])
            ->setAttribute('class', 'nav-item col-xs-1')
            ->setLinkAttribute('class', 'nav-link')
            ->setExtra('orderNumber', 1);
    }
}
