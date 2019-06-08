<?php
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
        $menu['Tools']->addChild('sberbank', ['route' => 'sberbank_report_index'])
            ->setAttribute('class', 'nav-item pl-3')
            ->setLinkAttribute('class', 'nav-link')
            ->setExtra('orderNumber', 2);
    }
}
