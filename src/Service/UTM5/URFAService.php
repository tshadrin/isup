<?php
namespace App\Service\UTM5;

use App\Entity\UTM5\UTM5UrfaUser;

class URFAService
{
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
    public function __construct($parameters) { $this->urfa = \URFAClient::init($parameters); }

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
}
