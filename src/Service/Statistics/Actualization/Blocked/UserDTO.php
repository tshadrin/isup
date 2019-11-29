<?php
declare(strict_types=1);

namespace App\Service\Statistics\Actualization\Blocked;

class UserDTO
{
    public const NO_TARIFFS = "нет тарифов";

    public $id;
    public $fullname;
    public $mobile;
    public $home;
    public $email;
    public $address;
    public $tariffs;
    public $group;
    public $flat_number;
    public $phone;
    public $balance;
}