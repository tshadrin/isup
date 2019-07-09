<?php

namespace App\Repository\UTM5;

use App\Entity\UTM5\UTM5User;
use App\Mapper\UTM5\UserMapper;

/**
 * Class UTM5UserRepository
 * @package App\Repository\UTM5
 */
class UTM5UserRepository
{
    /**
     * @var UserMapper
     */
    private $userMapper;

    public function __construct(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * @param int $id
     * @return UTM5User
     */
    public function findById(int $id): UTM5User
    {
        return $this->userMapper->getUserById($id);
    }

    /**
     * @param int $account
     * @return UTM5User
     */
    public function findByAccount(int $account): UTM5User
    {
        return $this->userMapper->getUserByAccount($account);
    }

    /**
     * @param string $login
     * @return UTM5User
     */
    public function findByLogin(string $login): UTM5User
    {
        return $this->userMapper->getUserByLogin($login);
    }

    /**
     * @param string $ip
     * @return UTM5User
     */
    public function findByIP(string $ip): UTM5User
    {
        return $this->userMapper->getUserByIP($ip);
    }

    public function findByFullName(string $fullName)
    {
        return $this->userMapper->getUsersByFullName($fullName);
    }

    public function findByAddress(string $address)
    {
        return $this->userMapper->getUsersByAddress($address);
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public function isUserPassportById(int $user_id): bool
    {
        return $this->userMapper->isUserPassport($user_id);
    }
}
