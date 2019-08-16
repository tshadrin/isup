<?php
declare(strict_types=1);

namespace App\Menu;

use App\Event\ConfigureMenuEvent;
use Knp\Menu\{ FactoryInterface, ItemInterface };
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Строитель меню
 * Class Builder
 * @package MenuBundle\Menu
 */
class Builder
{
    /**
     * Построение начального варианта меню и
     * добавление события создания меню
     * @param FactoryInterface $factory
     * @return ItemInterface
     */
    public function build(FactoryInterface $factory,
                          AuthorizationCheckerInterface $authorizationChecker,
                          EventDispatcherInterface $eventDispatcher): ItemInterface
    {
        $menu = $factory->createItem('Home', ['route' => 'default', 'childrenAttributes' => ['class' => 'navbar-nav'],]);

        $menu->addChild('Phones', ['route' => 'phone'])
            ->setAttribute('icon', 'fa fa-phone-alt')
            ->setExtra('dropdown', true)
            ->setExtra('orderNumber', 4)
            ->setExtra('routes', [
                ['route' => 'phone'],
                ['pattern' => '/^phone\..*/'],
            ])
        ;
        $menu['Phones']->addChild('List', ['route' => 'phone']);
        $menu['Phones']->addChild('Add', ['route' => 'phone.add']);
        $menu->addChild('vlans', ['route' => 'vlan'])
            ->setAttribute('icon', 'fas fa-network-wired')
            ->setExtra('dropdown', true)
            ->setExtra('orderNumber', 5)
            ->setExtra('routes', [
                ['route' =>'vlan'],
                ['pattern' => '/^vlan\..+/'],
            ])
        ;
        $menu['vlans']->addChild('List', ['route' => 'vlan']);
        $menu['vlans']->addChild('Add', ['route' => 'vlan.add']);
        if ($authorizationChecker->isGranted('ROLE_SUPPORT')) {
            $menu->addChild('Tools', ['uri' => '#'])
                ->setExtra('dropdown', true)
                ->setAttribute('icon', 'fas fa-toolbox')
            ;
            $menu['Tools']->addChild('NetPay', ['route' => 'netpay'])
                ->setExtra('orderNumber', 2)
            ;
            $menu['Tools']->addChild('Qiwi', ['route' => 'qiwi'])
                ->setExtra('orderNumber', 3)
            ;
            $menu->addChild('Channels', ['uri' => '/files/Kanaly_v_arendu.html'])
                ->setAttribute('icon', 'fas fa-project-diagram')
                ->setLinkAttribute('target', '_blank')
            ;
            $menu->addChild('Bitrix24', ['uri' => 'https://istranet.pro'])
                ->setAttribute('icon', 'fas fa-cloud')
                ->setLinkAttribute('target', '_blank')
            ;
            $menu['Tools']->addChild('Wi-Fi', ['uri' => '/files/Wi-fi.html'])
                ->setAttribute('icon', 'fa fa-wifi')
                ->setLinkAttribute('target', '_blank')
            ;
            //$menu->addChild('Profile', ['route' => 'fos_user_profile_show']);
            $menu['Tools']->addChild('profit_for_townships', ['route' => 'findmoney'])
                ->setExtra('orderNumber', 1)
            ;
            $menu['Tools']->addChild('Bot', ['uri' => 'http://bot.istra.news'])
                ->setLinkAttribute('target', '_blank')
            ;
            $menu['Tools']->addChild('FreePBX', ['uri' => 'https://inphone.istranet.ru'])
                ->setLinkAttribute('target', '_blank')
            ;
            $menu['Tools']->addChild('know_base', ['uri' => 'https://bz.istranet.ru'])
                ->setLinkAttribute('target', '_blank')
            ;
            $menu['Tools']->addChild('Dedovsk', ['uri' => 'http://dedovsk.istranet.ru/dedovsk/'])
                ->setLinkAttribute('target', '_blank')
            ;
            $menu['Tools']->addChild('Map', ['uri' => 'http://map.istranet.ru'])
                ->setLinkAttribute('target', '_blank')
            ;
            $menu['Tools']->addChild('Nagios', ['uri' => '/nagios/'])
                ->setLinkAttribute('target', '_blank')
            ;
            $menu['Tools']->addChild('UTM5 Admin', ['uri' => 'https://istranet.pro/~SUQNg'])
                ->setLinkAttribute('target', '_blank')
            ;
        }
        $menu->addChild('Control', ['uri' => '#'])
            ->setAttribute('icon', 'fa fa-user')
            ->setExtra('dropdown', true)
        ;
        if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu['Control']->addChild('admin', ['route' => 'sonata_admin_redirect']);
        }
        if ($authorizationChecker->isGranted('ROLE_SMS_ADMINISTRATOR')) {
            $menu['Control']->addChild('admin_sms', ['route' => 'admin_app_sms_smstemplate_list']);
        }
        $menu['Control']->addChild('Exit', ['route' => 'fos_user_security_logout']);

        $eventDispatcher->dispatch(
            new ConfigureMenuEvent($factory, $menu)
        );

        $this->reorderMenuItems($menu);

        return $menu;
    }

    /**
     * @param $menu
     */
    public function reorderMenuItems(ItemInterface $menu): void
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
