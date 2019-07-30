<?php
namespace App\Service\UTM5;

use App\Entity\UTM5\Passport;
use App\Entity\UTM5\UTM5UrfaUser;

class URFAService
{
    const PARAM_NUMBER = 4;
    const PARAM_ISSUED = 5;
    const PARAM_REGISTRATION = 6;
    const PARAM_AUTHORITYCODE = 7;
    const PARAM_BIRTHDAY = 8;
    /**
     * @var \URFAClient_API
     * Объект соединения с UTM5
     */
    private $urfa;

    /**
     * URFAService constructor.
     * @param $parameters
     * В конструкторе просто создает соединение с UTM5
     */
    public function __construct($parameters)
    {
        $this->urfa = \URFAClient::init($parameters);
    }

    /**
     * @return \URFAClient_API
     * Возвращаем объект соединения с UTM5
     */
    public function getUrfa() { return $this->urfa; }

    /**
     * @param $id
     * @return UTM5UrfaUser
     * Ищем пользователя по id
     */
    public function getUser($id) { return UTM5UrfaUser::findById($id, $this->urfa); }

    /**
     * @param $account_id
     * @return UTM5UrfaUser
     */
    public function getUserByAccount($account_id) {
        return UTM5UrfaUser::findByAccount($account_id, $this->urfa);
    }

    /**
     * Получение данных о рекомендуемом платеже из утм5
     * @param $account
     * @return float
     */
    public function getRequirementPaymentForUser($account): float
    {
        $tmp =  $this->urfa->rpcf_get_requirement_payment(['account_id' => $account]);
        return round($tmp['result'], 2);
    }

    /**
     * Поиск пользователя по аккаунту
     * @param $account
     * @return bool
     */
    public function searchUserByAccount($account)
    {
        $user = $this->urfa->rpcf_search_users_new([
            'select_type' => 0,
            'patterns_count'=> [
                0 => [
                    'what' => 3,
                    'criteria_id' => 3,
                    'pattern' => $account,
                ],
            ],
        ]);
        if(array_key_exists('user_data_size', $user) && (1 == count($user['user_data_size'])))
            return $user['user_data_size'][0];
        else
            return false;
    }

    /**
     * Получение информации о пользователе из утм5
     * @param $id
     * @return array|bool
     */
    public function getUserInfo($id)
    {
        $user = $this->urfa->rpcf_get_userinfo(['user_id' => $id]);
        if(array_key_exists('user_id', $user))
            return $user;
        else
            return false;
    }

    /**
     * Добавить пользователя в утм5 заполнив логин пароль и адрес и номер телефона
     * @param $login
     * @param $phone
     * @param $address
     * @return bool
     */
    public function addUser($login, $phone, $address, $name)
    {
        $user = $this->urfa->rpcf_add_user_new([
            'login' => $login,
            'full_name' => $name,
            'mob_tel' => $phone,
            'password' => mt_rand(10000000,99999999),
            'act_address' => $address,
            ]);
        if(array_key_exists('user_id', $user))
            return $user['user_id'];
        else
            return false;
    }

    /**
     * Удаление пользователя в утмке
     * @param $id
     * @return array
     */
    public function removeUser($id)
    {
        $data = $this->urfa->rpcf_remove_user(['user_id' => (int)$id,]);
        return $data;
    }

    /**
     * Редактирование поля с мобильным телефоном
     * @param $phone
     * @param $id
     */
    public function editMobilePhoneField($phone, $id)
    {
        $user = $this->getUserInfo($id);
        $user['parameters_count'] = $user['parameters_size'];
        $user['mob_tel'] = $phone;
        $this->urfa->rpcf_edit_user_new($user);
    }

    /**
     * @param $email
     * @param $id
     */
    public function editEmailField($email, $id)
    {
        $user = $this->getUserInfo($id);
        $user['parameters_count'] = $user['parameters_size'];
        $user['email'] = $email;
        $this->urfa->rpcf_edit_user_new($user);
    }

    /**
     * Получить текущий статус
     * Возвращает 0 1 при нормальной работе или false при ошибке
     * @param $id
     * @return bool
     */
    public function getInternetStatus(int $id)
    {
        $user = $this->getUserInfo($id);
        if (array_key_exists('basic_account', $user)) {
            $account = $this->getUrfa()->rpcf_get_accountinfo(['account_id' => $user['basic_account']]);
            return $account['int_status'];
        }
        return false;
    }

    /**
     * Смена статуса интернет
     * Возвращает true если статус поменялся
     * или false  если статус не поменялся
     * @param $id
     * @return bool
     */
    public function changeInternetStatus(int $id)
    {
        $current_status = $this->getInternetStatus($id);
        $this->getUrfa()->rpcf_change_intstat_for_user(['user_id' => $id, 'need_block' => $current_status ? 1 : 0,]);
        $new_status = $this->getInternetStatus($id);
        return $current_status !== $new_status;
    }

    /**
     * Изменение значение поля напоминания об оплате
     * @param int $id
     */
    public function changeRemindMe(int $id)
    {
        $user = $this->getUserInfo($id);
        if(array_key_exists('parameters_size', $user)) {
            $user['parameters_count'] = $user['parameters_size'];
            foreach($user['parameters_count'] as $k => $parameter) {
                if(3 === $parameter['parameter_id']) {
                    $user['parameters_count'][$k]['parameter_value'] = (1 == $parameter['parameter_value'])?'':1;
                }
            }
        }
        $this->getUrfa()->rpcf_edit_user_new($user);
    }

    /**
     * @param Passport $passport
     * @param int $id
     */
    public function editPassport(Passport $passport, int $id): void
    {
        $user = $this->getUserInfo($id);
        if(array_key_exists('parameters_size', $user)) {
            $user['parameters_count'] = $user['parameters_size'];
            foreach ($user['parameters_count'] as $num => $param) {
                if($param['parameter_id'] === self::PARAM_NUMBER) {
                    $user['parameters_count'][$num]['parameter_value'] = $passport->getNumber() ?? '';
                }
                if($param['parameter_id'] === self::PARAM_ISSUED) {
                    $user['parameters_count'][$num]['parameter_value'] = $passport->getIssued() ?? '';
                }
                if($param['parameter_id'] === self::PARAM_REGISTRATION) {
                    $user['parameters_count'][$num]['parameter_value'] = $passport->getRegistrationAddress() ?? '';
                }
                if($param['parameter_id'] === self::PARAM_AUTHORITYCODE) {
                    $user['parameters_count'][$num]['parameter_value'] = $passport->getAuthorityCode() ?? '';
                }
                if($param['parameter_id'] === self::PARAM_BIRTHDAY) {
                    $user['parameters_count'][$num]['parameter_value'] = $passport->getBirthday() ?? '';
                }
            }
        }
        $this->getUrfa()->rpcf_edit_user_new($user);
    }
}
