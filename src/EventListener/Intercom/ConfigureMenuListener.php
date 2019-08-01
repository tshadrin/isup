<?php
declare(strict_types=1);

namespace App\EventListener\Intercom;

use App\Event\ConfigureMenuEvent;

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
            ->setAttribute('icon', 'fa fa-calculator')
            ->setExtra('dropdown', true)
            ->setExtra('orderNumber', 3)
            ->setExtra('routes', [
                ['route' =>'intercom_'],
                ['pattern' => '/^intercom_.+/'],
            ])
        ;
        $menu['Intercoms']->addChild('List', ['route' => 'intercom_index']);
        $menu['Intercoms']->addChild('Add', ['route' => 'intercom_add']);
    }
}
