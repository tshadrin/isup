<?php
declare(strict_types=1);

namespace App\Service\Statistics\Actualization;

class UserDTO
{
    public const GROUP_NOV_MONTH = "платил в ноябре";
    public const GROUP_OCT_MONTH = "платил в октябре";
    public const GROUP_SEP_MONTH = "платил в сентябре";
    public const GROUP_AUG_MONTH = "платил в августе";
    public const GROUP_MANY_MONTH = "платил давно";
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
    public $month;
    public $phone;
}