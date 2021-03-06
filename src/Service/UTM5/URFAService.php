<?php
declare(strict_types=1);

namespace App\Service\UTM5;

use App\Entity\UTM5\MobilePhone;
use App\Entity\UTM5\Passport;
use App\Entity\UTM5\UTM5UrfaUser;
use ErrorException;
use URFAClient_API;

class URFAService
{
    const PARAM_REMIND_ME = 3;
    const PARAM_NUMBER = 4;
    const PARAM_ISSUED = 5;
    const PARAM_REGISTRATION = 6;
    const PARAM_AUTHORITYCODE = 7;
    const PARAM_BIRTHDAY = 8;
    const PARAM_ADDITIONAL_PHONE = 10;

    /** @var URFAClient_API */
    private $urfa;

    public function __construct(array $URFAParameters)
    {
        try {
            $this->urfa = \URFAClient::init($URFAParameters);
        } catch (ErrorException $e) {
            //@todo write to log
            $this->urfa = null;
        }
    }

    /**
     * @return URFAClient_API|null
     */
    public function getUrfa(): ?URFAClient_API
    {
        if(isset($this->urfa))
            return $this->urfa;
        throw new \DomainException("Urfa service is not started");
    }

    /**
     * @param int $account_id
     * @return UTM5UrfaUser
     */
    public function getUserByAccount(int $account_id)
    {
        return UTM5UrfaUser::findByAccount($account_id, $this->urfa);
    }

    /**
     * Получение данных о рекомендуемом платеже из утм5
     * @param $account
     * @return float
     */
    public function getRequirementPaymentForUser($account): float
    {
        $tmp =  $this->getUrfa()->rpcf_get_requirement_payment(['account_id' => $account]);
        return round($tmp['result'], 2);
    }

    /**
     * Поиск пользователя по аккаунту
     * @param $account
     * @return bool
     */
    public function searchUserByAccount($account)
    {
        $user = $this->getUrfa()->rpcf_search_users_new([
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
     * @param int $id
     * @return array
     */
    public function getUserInfo(int $id): array
    {
        $user = $this->getUrfa()->rpcf_get_userinfo(['user_id' => $id]);
        // hack to save additional fields
        $user['parameters_count'] = $user['parameters_size'];
        if (array_key_exists('user_id', $user)) {
            return $user;
        }
        throw new \DomainException("User not found");
    }

    public function editMobilePhone(string $phone, int $id): void
    {
        $user = $this->getUserInfo($id);
        $user['mob_tel'] = $phone;
        $this->urfa->rpcf_edit_user_new($user);
    }

    public function addComment(string $comment, int $id): void
    {
        $user = $this->getUserInfo($id);
        $user['comments'] .= "\n$comment";
        $this->urfa->rpcf_edit_user_new($user);
    }

    /**
     * @param string $phone
     * @param int $id
     */
    public function editAdditionalPhone(string $additionalPhone, int $id): void
    {
        $user = $this->getUserInfo($id);
        if (array_key_exists('parameters_size', $user)) {
            foreach($user['parameters_count'] as $k => $parameter) {
                if ($parameter['parameter_id'] === self::PARAM_ADDITIONAL_PHONE) {
                    $user['parameters_count'][$k]['parameter_value'] = $additionalPhone;
                }
            }
        }
        $this->getUrfa()->rpcf_edit_user_new($user);
    }

    /**
     * @param string $email
     * @param int $id
     */
    public function editEmail(string $email, int $id): void
    {
        $user = $this->getUserInfo($id);
        $user['email'] = $email;
        $this->urfa->rpcf_edit_user_new($user);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isInternetOn(int $id): bool
    {
        $user = $this->getUserInfo($id);
        if (array_key_exists('basic_account', $user)) {
            $account = $this->getUrfa()
                ->rpcf_get_accountinfo(['account_id' => $user['basic_account']]);
            return (bool)$account['int_status'];
        }
        throw new \DomainException("User status not found");
    }

    /**
     * @param int $id
     * @return bool
     */
    public function changeInternetStatus(int $id): bool
    {
        $internetStatus = $this->isInternetOn($id);
        $this->getUrfa()->rpcf_change_intstat_for_user(
            ['user_id' => $id, 'need_block' => $internetStatus ? 1 : 0,]
        );
        $newInternetStatus = $this->isInternetOn($id);
        return $internetStatus !== $newInternetStatus;
    }

    /**
     * @param int $id
     */
    public function changeRemindMe(int $id): void
    {
        $user = $this->getUserInfo($id);
        if (array_key_exists('parameters_size', $user)) {
            foreach($user['parameters_count'] as $k => $parameter) {
                if ($parameter['parameter_id'] === self::PARAM_REMIND_ME) {
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
        if (array_key_exists('parameters_size', $user)) {
            foreach ($user['parameters_count'] as $num => $param) {
                if ($param['parameter_id'] === self::PARAM_NUMBER) {
                    $user['parameters_count'][$num]['parameter_value'] = $passport->getNumber() ?? '';
                }
                if ($param['parameter_id'] === self::PARAM_ISSUED) {
                    $user['parameters_count'][$num]['parameter_value'] = $passport->getIssued() ?? '';
                }
                if ($param['parameter_id'] === self::PARAM_REGISTRATION) {
                    $user['parameters_count'][$num]['parameter_value'] = $passport->getRegistrationAddress() ?? '';
                }
                if ($param['parameter_id'] === self::PARAM_AUTHORITYCODE) {
                    $user['parameters_count'][$num]['parameter_value'] = $passport->getAuthorityCode() ?? '';
                }
                if ($param['parameter_id'] === self::PARAM_BIRTHDAY) {
                    $user['parameters_count'][$num]['parameter_value'] = $passport->getBirthday() ?? '';
                }
            }
        }
        $this->getUrfa()->rpcf_edit_user_new($user);
    }

    /**
     * @param $login
     * @param $phone
     * @param $address
     * @param $name
     * @return bool
     */
    public function addUser($login, $phone, $address, $name)
    {
        $user = $this->getUrfa()->rpcf_add_user_new([
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
     * @param $id
     * @return array
     */
    public function removeUser($id)
    {
        $data = $this->getUrfa()->rpcf_remove_user(['user_id' => (int)$id,]);
        return $data;
    }
}
