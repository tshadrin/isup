<?php
declare(strict_types=1);

namespace App\Service\UTM5\CallRegister;


class Command
{
    /**
     * @var string
     */
    public $operatorNumber;
    /**
     * @var string
     */
    public $callerNumber;

    public function __construct(string $operatorNumber, string $callerNumber)
    {
        $this->operatorNumber = $operatorNumber;
        $this->callerNumber = $callerNumber;
    }
}