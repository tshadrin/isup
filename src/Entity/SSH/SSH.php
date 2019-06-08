<?php
namespace App\Entity\SSH;

class SSH
{
    /**
     * @var string
     * Сервер для соединения
     */
    private $host = "";
    /**
     * @var string
     * Порт для соединения
     */
    private $port = "";
    /**
     * @var string
     * Полный составленный пароль к серверу
     */
    private $pass = "";
    /**
     * @var
     * Дескриптор соединения ssh
     */
    private $conn;

    private $config;
    /**
     * SSH constructor.
     * @param $host
     * @param $config
     * Конструктор принмает ip сервера с которым нужно соедениться
     * Вычисляет номер порта к серверу
     * Вычисляет пароль к серверу
     * Соединяется с сервером по SSH
     * Авторизуется и запускает интерпретатор
     */
    public function __construct($host, $config)
    {
        $this->config = $config;
        $this->host = $host;
        $data = explode(".",$host);
        $this->port = $this->_calcPort($data);
        $this->pass = $this->_calcPass($data);
        $this->_sshConnect();
        $this->_sshAuth();
        $this->_sshShell();
    }

    /**
     * @param $data
     * @return string
     * Вычислить номер порта
     */
    private function _calcPort($data)
    {
        return (int)$data[1].$data[2]."0".$data[3];
    }

    /**
     * @param $data
     * @return string
     * Вычислить пароль сервера
     */
    private function _calcPass($data)
    {
        return $this->config['prefix'].$this->config['servers'][$data[3]].$this->config['suffix'];
    }

    /**
     * Метод создает дескриптор соединения ssh
     */
    private function _sshConnect()
    {
        if (!$this->conn = ssh2_connect($this->host,$this->port))
            die("Ошибка соединения с сервером");
    }

    /**
     * Метод авторизации по ssh
     * Работает только после соединения
     */
    private function _sshAuth()
    {
        if (!ssh2_auth_password($this->conn,$this->config['user'],$this->pass))
            die("Ошибка авторизации");
    }

    /**
     * Установка интерпретатора
     */
    private function _sshShell()
    {
        ssh2_shell($this->conn,"bash");
    }

    /**
     * @param $command
     * @return string
     * Выполнение команды с сервера
     */
    public function ssh_exec($command)
    {
        $stream = ssh2_exec($this->conn,$command);
        stream_set_blocking($stream,true);
        $data = "";
        while ($o = fgets($stream)) {
            $data .= $o;
        }
        return $data;
    }

    /**
     * @param $host
     * @return bool или string
     * Проверка наличия включенной опции турбо у клиента на сервере
     * Возвращает false если пользователя нет в таблице или
     * количество оставшихся секунд, если пользователь есть в таблице
     */
    public function checkInTurboTable($host)
    {
        $data = $this->ssh_exec("ipset -L TURBO | grep {$host}");
        if (empty($data)) {
            return false;
        } else {
            $data = explode(' ', $data);
            return $data[2];
        }
    }


    /**
     * Метод проверяет наличие ip в таблице USER_TMP
     * на сервере, с которым установлено соединение
     * @param $ip string
     * @return bool
     */
    public function hasOpenTemporary($ip)
    {
        $data = $this->ssh_exec("ipset -L USER_TMP | grep {$ip}");

        if (strlen($data))
            return true;
        else
            return false;
    }
    /**
     * Метод проверяет наличие ip в таблице USER_TMP
     * на сервере, с которым установлено соединение
     * @param $ip string
     * @return bool
     */
    public function hasTurbo($ip)
    {
        $data = $this->ssh_exec("ipset -L TURBO | grep {$ip}");

        if (strlen($data))
            return true;
        else
            return false;
    }

    /**
     * Метод добавляет ip в таблицу USER_TMP на сервере,
     * с которым установлено соединение
     * @param $ip string
     */
    public function enableTurbo($ip)
    {
        $this->ssh_exec("ipset add TURBO {$ip}");
    }

    /**
     * Метод добавляет ip в таблицу USER_TMP на сервере,
     * с которым установлено соединение
     * @param $ip string
     */
    public function openTemporary($ip)
    {
        $this->ssh_exec("ipset add USER_TMP {$ip}");
    }
}
