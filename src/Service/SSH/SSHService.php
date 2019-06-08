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
     * @param $ip
     * @param $host
     * @return bool
     * Проверяем, можно ли включить опцию турбо у юзера
     * Возвращаем количество оставшихся секунд опции турбо или false, если опция не активирована
     */
    public function checkTurboForUser($ip, $host)
    {
        $connection = $this->getConnection($host);
        $result =  $connection->checkInTurboTable($ip);
        return $result;
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
    public function openTurbo($ip, $router)
    {
        $connection = $this->getConnection($router);
        if($connection->hasTurbo($ip))
            return true;
        else
            $connection->enableTurbo($ip);
        if($connection->hasTurbo($ip))
            return true;
        else
            return false;
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
