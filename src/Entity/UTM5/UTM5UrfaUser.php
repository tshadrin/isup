<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use App\Collection\UTM5\{ GroupCollection, RouterCollection, ServiceCollection, TariffCollection };

class UTM5UrfaUser extends UTM5User
{
    const MIN_ROUTER_GROUP = 300;
    const MAX_ROUTER_GROUP = 350;

    /**
     * @var \URFAClient_API
     */
    private $urfa;

    /**
     * UTM5UrfaUser constructor.
     * @param \URFAClient_API $urfa
     */
    public function __construct(\URFAClient_API $urfa)
    {
        $this->urfa = $urfa;
    }

    /**
     * Поиск ip адресов по сервисам тарифа с типом 3
     * rpcf_get_iptraffic_service_link_ipv6 - поиск данных по сервисной связке
     * @return array
     */
    public function getIps(): array
    {
        if(is_null($this->ips)) {
            foreach ($this->getTariffs() as $tariff) {
                if(($services = $tariff->getServices()) instanceof ServiceCollection) {
                    foreach ($services as $service) {
                        if (3 === $service->getType()) {
                            $linkV6Info = $this->urfa->rpcf_get_iptraffic_service_link_ipv6(['slink_id' => $service->getLink(),]);
                            foreach ($linkV6Info['ip_groups_count'] as $ipgroup) {
                                if(32 == $ipgroup['mask']){
                                    $this->ips[] = $ipgroup['ip_address'];
                                }
                            }
                        }
                    }
                }
            }
        }
        return parent::getIps();
    }

    /**
     * Получение тарифов пользователя
     * rpcf_get_user_tariffs - поиск тарифов пользователя
     * rpcf_get_tariff - получение данных тарифа
     * rpcf_get_discount_period - получение рассчетного периода
     * @return TariffCollection
     */
    public function getTariffs(): TariffCollection
    {
        if (is_null($this->tariffs)) {
            $tariffsData = $this->urfa->rpcf_get_user_tariffs(['user_id' => $this->id,]);
            $tariffs = new TariffCollection();
            foreach($tariffsData['user_tariffs_size'] as $tariffData) {
                $services = $this->findTariffServicesByAccount($this->getAccount(), $tariffData['tariff_link_id_array']);
                $tariffInfoData = $this->urfa->rpcf_get_tariff(['tariff_id' => $tariffData['tariff_current_array']]);
                $tariffInfoNextData = $this->urfa->rpcf_get_tariff(['tariff_id' => $tariffData['tariff_next_array']]);
                $dpData = $this->urfa->rpcf_get_discount_period(['discount_period_id' => $tariffData['discount_period_id_array'],]);
                $tariff = new Tariff($tariffInfoData['tariff_name'], $tariffInfoNextData['tariff_name'], new DiscountPeriod(
                    $tariffData['discount_period_id_array'],
                    \DateTimeImmutable::createFromFormat("U", (string)$dpData['start_date']),
                    \DateTimeImmutable::createFromFormat("U", (string)$dpData['end_date']))
                );
                $tariff->setServices($services);
                $tariffs->add($tariff);
            }
            count($tariffs) > 0?$this->setTariffs($tariffs):null;
        }
        return parent::getTariffs();
    }

    /**
     * Получение сервисов для тарифных планов пользователя
     * rpcf_get_all_services_for_user - все сервисы пользователя
     * rpcf_get_periodic_service_link - получение сервисныз связок для серисов
     * @param int $account - лицевой счет
     * @param int $tariff_link - тарифная связка
     * @return ServiceCollection|null
     */
    private function findTariffServicesByAccount(int $account, int $tariff_link): ?ServiceCollection
    {
        $tariffServices = new ServiceCollection();

        $servicesData = $this->urfa->rpcf_get_all_services_for_user([
            'account_id' => $account,
        ]);
        foreach($servicesData['slink_id_count'] as $serviceData) {
            if($serviceData['service_type_array'] === 3) {
                $slinkData = $this->urfa->rpcf_get_periodic_service_link([
                    'slink_id' => $serviceData['slink_id_array'],
                ]);
                if ($tariff_link === $slinkData['tariff_link_id']) {

                    $service = new Service($serviceData['service_name_array'], $serviceData['service_cost_array']);
                    $service->setLink($serviceData['slink_id_array']);
                    $service->setType($serviceData['service_type_array']);
                    $tariffServices->add($service);
                }
            }
        }
        return count($tariffServices) > 0?$tariffServices:null;
    }

    /**
     * Получение групп пользователя
     * rpcf_get_groups_for_user
     * @return GroupCollection
     */
    public function getGroups(): GroupCollection
    {
        if (is_null($this->groups)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $result  = $this->urfa->rpcf_get_groups_for_user(['user_id' => $this->id,]);
            $groups = new GroupCollection();
            foreach ($result['groups_size'] as $row){
                $groups->add(new Group($row['group_id'], $row['group_name']));
            }
            $this->setGroups($groups);
        }
        return parent::getGroups();
    }

    /**
     * Получение роутеров пользователя
     * rpcf_get_fwrules_list_new - список правил файерволлов
     * rpcf_get_routers_list - список роутеров
     * @return RouterCollection|null
     */
    public function getRouters(): ?RouterCollection
    {
        if(is_null($this->routers)) {
            $groups = $this->getGroups();
            $routers = new RouterCollection();
            $firewallRules = $this->urfa->rpcf_get_fwrules_list_new();
            $routersData = $this->urfa->rpcf_get_routers_list();
            foreach ($groups as $group) {
                if (self::MIN_ROUTER_GROUP <= $group->getId() && self::MAX_ROUTER_GROUP >= $group->getId()) {
                    foreach ($firewallRules['rules_count'] as $rule) {
                        if ($rule['group_id'] === $group->getId()) {
                            foreach ($routersData['routers_size'] as $row) {
                                if ($rule['router_id'] === $row['router_id']) {
                                    $routers->add(new Router($row['router_comments'], $row['router_ip']));
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
            }
            count($routers) > 0?$this->setRouters($routers):null;
        }
        return parent::getRouters();
    }

    /**
     * @param int $account
     * @return $this
     */
    public function loadByAccount(int $account)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $data = $this->urfa->rpcf_search_users_new(['select_type' => 0,
                                                    'patterns_count' => [
                                                        0 => [
                                                            'what' => 3,
                                                            'criteria_id'=> 3,
                                                            'pattern' => $account
                                                        ],
                                                    ],
        ]);
        $id = $data['user_data_size']['0']['user_id'];
        /** @noinspection PhpUndefinedMethodInspection */
        $data = $this->urfa->rpcf_get_userinfo(['user_id' => $id,]);
        $this->id = $data['user_id'];
        $this->full_name = $data['full_name'];
        $this->login = $data['login'];
        $this->account = $data['basic_account'];
        $this->addresses['jur_address'] = $data['jur_address'];
        $this->addresses['act_address'] = $data['act_address'];
        if (!empty($data['flat_number']))
            $this->addresses['act_address'] .= " {$data['flat_number']}";
        $this->phones['work_tel'] = $data['work_tel'];
        $this->phones['home_tel'] = $data['home_tel'];
        $this->phones['mob_tel'] = $data['mob_tel'];
        return $this;
    }

    /**
     * @param string $account
     * @param \URFAClient_API $urfa
     * @return UTM5UrfaUser
     */
    public static function findByAccount(int $account, \URFAClient_API $urfa): self
    {
        $user = new self($urfa);
        return $user->loadByAccount($account);
    }
}
