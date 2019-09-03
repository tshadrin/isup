<?php
declare(strict_types=1);

namespace App\Service\UTM5\CallRegister;


class Command
{
    /** @var int  */
    public $operatorNumber;
    /** @var string  */
    public $callerNumber;

    public function __construct(int $operatorNumber, string $callerNumber)
    {
        $this->operatorNumber = $operatorNumber;
        $this->callerNumber = $callerNumber;
    }
}