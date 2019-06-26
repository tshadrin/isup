<?php
namespace App\Entity\UTM5;

use App\Collection\UTM5\GroupCollection;
use App\Collection\UTM5\RouterCollection;
use App\Collection\UTM5\ServiceCollection;

class UTM5UrfaUser extends UTM5User
{
    /**
     * @var \URFAClient_API
     */
    private $urfa;

    private $slink_id;
    private $router_group_ids = [];

    /**
     * @param \URFAClient_API $urfa
     * В конструкторе передаем объект клиента к UTM5
     */
    public function __construct(\URFAClient_API $urfa) { $this->urfa = $urfa; }

    /**
     * @return array
     * Подгружаем сервисы при необходимости
     */
    public function getServices(): ServiceCollection
    {
        if (empty($this->services)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $services = $this->urfa->rpcf_get_all_services_for_user(['account_id' => $this->account]);
            foreach ($services['slink_id_count'] as $service) {
                $this->services[] = $service['service_name_array'];
                if ($service['service_type_array'] == 3)
                    $this->slink_id = $service['slink_id_array'];
            }
        }
        return parent::getServices();
    }

    /**
     * @return array
     * Подгружаем ip адреса клиента при необходимости
     */
    public function getIps(): array
    {
        if (empty($this->ips)) {
            if (empty($this->slink_id)){
                $this->getServices();
            }
            $user_service_link = $this->urfa->rpcf_get_iptraffic_service_link(['slink_id' => $this->slink_id,]);
            foreach ($user_service_link['ip_groups_count'] as $ipgroup) {
                $this->ips[] = long2ip($ipgroup['ip_address']);
            }
        }
        return parent::getIps();
    }

    /**
     *  Подгружаем информацию о тарифе
     */
    public function getTariff()
    {
        if (empty($this->tariff)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $tariff = $this->urfa->rpcf_get_user_tariffs(['user_id' => $this->id,]);
            /** @noinspection PhpUndefinedMethodInspection */
            $current_tariff = $this->urfa->rpcf_get_tariff(['tariff_id' => $tariff['user_tariffs_size'][0]['tariff_current_array'],]);
            $this->tariff['current'] = $current_tariff['tariff_name'];
            /** @noinspection PhpUndefinedMethodInspection */
            $next_tariff = $this->urfa->rpcf_get_tariff(['tariff_id' => $tariff['user_tariffs_size'][0]['tariff_next_array'],]);
            $this->tariff['next'] = $next_tariff['tariff_name'];
        }
        return parent::getTariff();
    }

    public function getGroups(): GroupCollection
    {
        if (empty($this->groups)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $groups = $this->urfa->rpcf_get_groups_for_user(['user_id' => $this->id,]);
            foreach ($groups['groups_size'] as $group){
                if ($group['group_id'] > 299 && $group['group_id'] < 400) {
                    $this->router_group_ids[] = $group['group_id'];
                }
                $this->groups[] = $group['group_name'];
            }
        }
        return parent::getGroups();
    }

    /**
     * @return mixed - данные о сервере
     * Ищем данные о сервере пользователя
     */
    public function getRouters(): RouterCollection
    {
        if (empty($this->groups)) {
            $this->getGroups();
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $firewall_rules = $this->urfa->rpcf_get_fwrules_list_new();
        /** @noinspection PhpUndefinedMethodInspection */
        $routers = $this->urfa->rpcf_get_routers_list();
        foreach ($firewall_rules['rules_count'] as $rule){
            if (in_array($rule['group_id'], $this->router_group_ids)) {
                while (($i = array_search($rule['group_id'], $this->router_group_ids)) !== false) {
                    unset($this->router_group_ids[$i]);
                }
                foreach ($routers['routers_size'] as $router) {
                    if ($rule['router_id'] == $router['router_id']) {
                        $r['ip'] = $router['router_ip'];
                        $r['name'] = $router['router_comments'];
                        $this->routers[] = $r;
                    }
                }
            }
        }
        return parent::getRouters();
    }

    /**
     * @param $id
     * @return $this
     * Загрузка данных пользователя
     */
    public function load($id)
    {
        if(empty($this->id)) {
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
        }
        return $this;
    }

    public function loadByAccount($account)
    {
        $account = (int) $account;
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
     * @param $id
     * @param \URFAClient_API $urfa
     * @return UTM5UrfaUser
     */
    public static function findById($id, \URFAClient_API $urfa)
    {
        $user = new self($urfa);
        return $user->load($id);
    }

    public static function findByAccount($account, $urfa)
    {
        $user = new self($urfa);
        return $user->loadByAccount($account);
    }
}
