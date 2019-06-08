<?php
namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use App\Event\ConfigureMenuEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Строитель меню
 * Class Builder
 * @package MenuBundle\Menu
 */
class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Построение начального варианта меню и
     * добавление события создания меню
     * @param FactoryInterface $factory
     * @return ItemInterface
     */
    public function build(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker, EventDispatcherInterface $eventDispatcher)
    {
        $menu = $factory->createItem('root', ['childrenAttributes' => ['class' => 'navbar-nav'],]);

        $menu->addChild('Phones', ['route' => 'phone_default'])
            ->setAttribute('class', 'dropdown')
            ->setLinkAttribute('data-toggle', 'dropdown')
            ->setLinkAttribute('class', 'dropdown-toggle nav-link')
            ->setChildrenAttribute('class', 'dropdown-menu bg-nav-dropdown m-1')
            ->setChildrenAttribute('role', 'menu')
            ->setExtra('orderNumber', 4)
        ;
        $menu['Phones']->addChild('List', ['route' => 'phone_default'])
            ->setAttribute('class', 'nav-item pl-3')
            ->setLinkAttribute('class', 'nav-link');
        $menu['Phones']->addChild('Add', ['route' => 'phone_add'])
            ->setAttribute('class', 'nav-item pl-3')
            ->setLinkAttribute('class', 'nav-link');
        $menu->addChild('vlans', ['route' => 'vlan_default'])
            ->setAttribute('class', 'dropdown')
            ->setLinkAttribute('data-toggle', 'dropdown')
            ->setLinkAttribute('class', 'dropdown-toggle nav-link')
            ->setChildrenAttribute('class', 'dropdown-menu bg-nav-dropdown m-1')
            ->setChildrenAttribute('role', 'menu')
            ->setExtra('orderNumber', 5)
        ;
        $menu['vlans']->addChild('List', ['route' => 'vlan_default'])
            ->setAttribute('class', 'nav-item pl-3')
            ->setLinkAttribute('class', 'nav-link');
        $menu['vlans']->addChild('Add', ['route' => 'vlan_add'])
            ->setAttribute('class', 'nav-item pl-3')
            ->setLinkAttribute('class', 'nav-link');
        if ($authorizationChecker->isGranted('ROLE_SUPPORT')) {

            $menu->addChild('Tools', ['uri' => '#'])
                ->setAttribute('class', 'dropdown')
                ->setLinkAttribute('data-toggle', 'dropdown')
                ->setLinkAttribute('class', 'dropdown-toggle nav-link')
                ->setChildrenAttribute('class', 'dropdown-menu dropdown-menu-right bg-nav-dropdown m-1')
                ->setChildrenAttribute('role', 'menu');

            $menu->addChild('Channels', ['uri' => '/files/Kanaly_v_arendu.html'])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');
            $menu->addChild('Wi-Fi', ['uri' => '/files/Wi-fi.html'])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');
            //$menu->addChild('Profile', ['route' => 'fos_user_profile_show']);

            $menu['Tools']->addChild('profit_for_townships', ['route' => 'find_money'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link')
                ->setExtra('orderNumber', 1);
            $menu['Tools']->addChild('Bot', ['uri' => 'http://bot.istra.news'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link');
            $menu['Tools']->addChild('FreePBX', ['uri' => 'https://inphone.istranet.ru'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link');
            $menu['Tools']->addChild('know_base', ['uri' => 'https://bz.istranet.ru'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link');
            $menu['Tools']->addChild('Dedovsk', ['uri' => 'http://dedovsk.istranet.ru/dedovsk/'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link');
            $menu['Tools']->addChild('Map', ['uri' => 'http://map.istranet.ru'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link');
            $menu['Tools']->addChild('Nagios', ['uri' => '/nagios/'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link');
            $menu['Tools']->addChild('UTM5 Admin', ['uri' => '/files/utm5_admin.zip'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link');

        }
        $menu->addChild('Control', ['uri' => '#'])
            ->setAttribute('class', 'dropdown')
            ->setLinkAttribute('data-toggle', 'dropdown')
            ->setLinkAttribute('class', 'dropdown-toggle nav-link')
            ->setChildrenAttribute('class', 'dropdown-menu dropdown-menu-right bg-nav-dropdown  m-1')
            ->setChildrenAttribute('role', 'menu');
        if ($authorizationChecker->isGranted('ROLE_ADMIN'))
            $menu['Control']->addChild('admin', ['route' => 'sonata_admin_redirect'])
                ->setAttribute('class', 'nav-item pl-3')
                ->setLinkAttribute('class', 'nav-link');
        $menu['Control']->addChild('Exit', ['route' => 'fos_user_security_logout'])
            ->setAttribute('class', 'nav-item pl-3')
            ->setLinkAttribute('class', 'nav-link');
        // Добавление события в диспетчер
        $eventDispatcher->dispatch(
            new ConfigureMenuEvent($factory, $menu),
            ConfigureMenuEvent::CONFIGURE
        );
        $this->reorderMenuItems($menu);
        return $menu;
    }

    public function reorderMenuItems($menu)
    {
        $menuOrderArray = array();
        $addLast = array();
        $alreadyTaken = array();

        foreach ($menu->getChildren() as $key => $menuItem) {
            if ($menuItem->hasChildren()) {
                $this->reorderMenuItems($menuItem);
            }

            $orderNumber = $menuItem->getExtra('orderNumber');

            if ($orderNumber != null) {
                if (!isset($menuOrderArray[$orderNumber])) {
                    $menuOrderArray[$orderNumber] = $menuItem->getName();
                } else {
                    $alreadyTaken[$orderNumber] = $menuItem->getName();
                    // $alreadyTaken[] = array('orderNumber' => $orderNumber, 'name' => $menuItem->getName());
                }
            } else {
                $addLast[] = $menuItem->getName();
            }
        }

        // sort them after first pass
        ksort($menuOrderArray);

        // handle position duplicates
        if (count($alreadyTaken)) {
            foreach ($alreadyTaken as $key => $value) {
                // the ever shifting target
                $keysArray = array_keys($menuOrderArray);

                $position = array_search($key, $keysArray);

                if ($position === false) {
                    continue;
                }

                $menuOrderArray = array_merge(array_slice($menuOrderArray, 0, $position), array($value), array_slice($menuOrderArray, $position));
            }
        }

        // sort them after second pass
        ksort($menuOrderArray);

        // add items without ordernumber to the end
        if (count($addLast)) {
            foreach ($addLast as $key => $value) {
                $menuOrderArray[] = $value;
            }
        }

        if (count($menuOrderArray)) {
            $menu->reorderChildren($menuOrderArray);
        }
    }
}
