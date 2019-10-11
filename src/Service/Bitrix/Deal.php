<?php
declare(strict_types = 1);

namespace App\Service\Bitrix;

class Deal
{
    /** @var int  */
    public $id;
    /** @var string  */
    public $status;
    /** @var int  */
    public $utm5Id;
    /** @var string  */
    public $address;
    /** @var string  */
    public $phone;
    /** @var string  */
    public $name;

    public function __construct(int $id, string $status, int $utm5Id, string $address, string $phone, string $name)
    {
        $this->id = $id;
        $this->status = $status;
        $this->utm5Id = $utm5Id;
        $this->address = $address;
        $this->phone = $phone;
        $this->name = $name;
    }
}