<?php
declare(strict_types=1);

namespace App\Event;

use Knp\Menu\{ FactoryInterface, ItemInterface };
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Событие построения меню
 * Class ConfigureMenuEvent
 * @package App\Event
 */
class ConfigureMenuEvent extends GenericEvent
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ItemInterface
     */
    private $menu;

    /**
     * ConfigureMenuEvent конструктор.
     * @param FactoryInterface $factory
     * @param ItemInterface $menu
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        parent::__construct();
        $this->factory = $factory;
        $this->menu = $menu;
    }

    /**
     * Возвращает фабрику меню
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Возвращает меню
     * @return ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
