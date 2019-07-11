<?php
declare(strict_types = 1);


namespace App\Entity\Zabbix;


class Statement
{
    private $message;
    /**
     * @var array
     */
    private $params;

    public function __construct($message, array $params = [])
    {

        $this->message = $message;
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }
}