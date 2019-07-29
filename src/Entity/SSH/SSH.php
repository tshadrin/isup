<?php
declare(strict_types=1);

namespace App\Entity\SSH;

class SSH
{
    const SHELL_NAME = 'bash';
    /**
     * @var string
     * Сервер для соединения
     */
    private $host;
    /**
     * @var int
     * Порт для соединения
     */
    private $port;
    /**
     * @var string
     * Полный составленный пароль к серверу
     */
    private $password;
    /**
     * @var resource
     * Дескриптор соединения ssh
     */
    private $connection;
    /**
     * @var array
     */
    private $config;

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return resource
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param resource $connection
     */
    public function setConnection($connection): void
    {
        $this->connection = $connection;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * В конструкторе вычисляются порт и пароль для хоста.
     * Далее устанавливается соединение, происходит авторизация пользователя
     * и загружается интерпретатор(bash)
     * SSH constructor.
     * @param string $host
     * @param array $config
     */
    public function __construct(string $host, array $config)
    {
        $this->setConfig($config);
        $this->setHost($host);
        $this->setPort($this->calculatePort($host));
        $this->setPassword($this->calculatePassword($host));
        $connection = $this->connect($this->getHost(), $this->getPort());
        $this->setConnection($connection);
        $this->auth($this->getConnection(), $config['user'], $this->getPassword());
        $this->shell($this->getConnection());
    }

    /**
     * @param string $host
     * @return int
     */
    private function calculatePort(string $host): int
    {
        $data = explode(".", $host);
        return (int)"{$data[1]}{$data[2]}0{$data[3]}";
    }

    /**
     * @param string $host
     * @return string
     */
    private function calculatePassword(string $host): string
    {
        $data = explode(".", $host);
        return "{$this->config['prefix']}{$this->config['servers'][$data[3]]}{$this->config['suffix']}";
    }

    /**
     * @param string $host
     * @param int $port
     * @return resource
     */
    private function connect(string $host, int $port)
    {
        if (!($connection = ssh2_connect($host, $port))) {
            throw new \DomainException("SSH Connection error");
        }
        return $connection;
    }

    /**
     * @param $connection
     * @param string $user
     * @param string $password
     */
    private function auth($connection, string $user, string $password): void
    {
        if (!ssh2_auth_password($connection, $user, $password)) {
            throw new \DomainException("SSH Authentication error");
        }
    }

    /**
     * @param $connection
     */
    private function shell($connection): void
    {
        if(!ssh2_shell($connection, self::SHELL_NAME)) {
            throw new \DomainException("SSH Shell error");
        }
    }

    /**
     * Выполнение команды на сервере
     * @param $command
     * @return string
     */
    public function exec($command): string
    {
        $stream = ssh2_exec($this->getConnection(), $command);
        stream_set_blocking($stream,true);
        $output = "";
        while ($data = fgets($stream)) {
            $output .= $data;
        }
        return $output;
    }

    /**
     * Метод проверяет наличие ip в таблице USER_TMP
     * на сервере, с которым установлено соединение
     * @param string $ip
     * @return bool
     */
    public function isOpenTemporary(string $ip): bool
    {
        $data = $this->exec("ipset -L USER_TMP | grep {$ip}");
        return empty($data)?false:true;
    }

    /**
     * Метод добавляет ip в таблицу USER_TMP на сервере,
     * с которым установлено соединение
     * @param string $ip
     */
    public function openTemporary(string $ip): void
    {
        $this->exec("ipset add USER_TMP {$ip}");
    }

    /**
     * Проверяет есть ли данные в сете турбо по ip
     * @param string $ip
     * @return int|null
     */
    public function checkInTurboTable(string $ip): ?int
    {
        $data = $this->exec("ipset -L TURBO | grep {$ip}");
        if(!empty($data)) {
            $data = explode(' ', $data);
            return (int)$data[2];
        }
        return null;
    }

    /**
     * Метод проверяет наличие ip в таблице USER_TMP
     * на сервере, с которым установлено соединение
     * @param string $ip
     * @return bool
     */
    public function isTurbo(string $ip): bool
    {
        $data = $this->exec("ipset -L TURBO | grep {$ip}");
        return empty($data)?false:true;
    }

    /**
     * Метод добавляет ip в таблицу USER_TMP на сервере,
     * с которым установлено соединение
     * @param string $ip
     */
    public function enableTurbo(string $ip): void
    {
        $this->exec("ipset add TURBO {$ip}");
    }
}
