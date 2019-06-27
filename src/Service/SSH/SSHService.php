<?php
namespace App\Service\SSH;

use App\Entity\SSH\SSH;

class SSHService
{
    /**
     * @var array
     * Параметры соединения по ssh
     */
    private $parameters;

    /**
     * SSHService constructor.
     * @param $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param $host
     * @return SSH
     * Создаем соединение и возвращаем его объект
     *
     */
    public function getConnection($host)
    {
        //TODO сделать провеку на существование и сделать соединение полем класса
        return new SSH($host, $this->parameters);
    }

    /**
     * Возвращает количество секунд турбо-режима, если он включен
     * @param string $ip
     * @param string $serverIp
     * @return int|null
     */
    public function checkTurboForUser(string $ip, string $serverIp): ?int
    {
        $connection = $this->getConnection($serverIp);
        return $connection->checkInTurboTable($ip);
    }

    /**
     * Проверяет включена ли опция турбо
     * @param string $ip
     * @param string $serverIp
     * @return bool
     */
    public function isTurbo(string $ip, string $serverIp): bool
    {
        $connection = $this->getConnection($serverIp);
        return $connection->isTurbo($ip);
    }

    /**
     * Временно открывает доступ в интернет на сервере
     * Если доступ уже открыт или получилось открыть доступ
     * - возвращает true
     * Если открыть доступ не удалось - возвращает false
     * @param string $ip
     * @param string $serverIp
     * @return bool
     */
    public function openTurbo(string $ip, string $serverIp): bool
    {
        $connection = $this->getConnection($serverIp);
        $connection->enableTurbo($ip);

        return $connection->isTurbo($ip)?true:false;
    }
    /**
     * Временно открывает доступ в интернет на сервере
     * Если доступ уже открыт или получилось открыть доступ
     * - возвращает true
     * Если открыть доступ не удалось - возвращает false
     * @param $ip string
     * @param $router string
     * @return bool
     */
    public function openInternetTemporary($ip, $router)
    {
        $connection = $this->getConnection($router);
        if($connection->hasOpenTemporary($ip))
            return true;
        else
            $connection->openTemporary($ip);
        if($connection->hasOpenTemporary($ip))
            return true;
        else
            return false;
    }
}
