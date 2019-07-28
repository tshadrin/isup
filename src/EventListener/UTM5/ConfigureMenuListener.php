<?php
declare(strict_types=1);

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
     * Добавление в меню пунктов для этого бандла
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $menu->addChild('Search in UTM5', ['route' => 'search'])
            ->setAttribute('class', 'col-xs-1')
            ->setAttribute('icon', 'fa fa-search')
            ->setExtra('orderNumber', 1)
            ->setExtra('routes', [
                ['route' =>'search'],
                ['pattern' => '/^search.+/'],
                ['pattern' => '/^order_add_from_user/'],
            ])
        ;
    }
}
