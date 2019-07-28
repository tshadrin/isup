<?php
declare(strict_types=1);

namespace App\EventListener\SberbankReport;

use App\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Обработчик события создания меню
 * Class ConfigureMenuListener
 * @package App\EventListener\SberbankReport
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
        $menu['Tools']->addChild('sberbank', ['route' => 'sberbank_report_index'])
            ->setExtra('orderNumber', 2)
            ->setExtra('routes', [
                ['route' =>'sberbank_report_index'],
                ['route' =>'sberbank_log'],
            ])
        ;
    }
}
