<?php
declare(strict_types = 1);


namespace App\Entity\Zabbix;


use Webmozart\Assert\Assert;

class Statement
{
    private $message;
    /**
     * @var array
     */
    private $params;

    public function __construct($message, array $params = [])
    {
        Assert::notEmpty($message);

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
}