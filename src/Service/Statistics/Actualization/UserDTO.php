<?php
declare(strict_types=1);

namespace App\Service\Statistics\Actualization;

class UserDTO
{
    public const GROUP_NOV_MONTH = "в ноябре";
    public const GROUP_OCT_MONTH = "в октябре";
    public const GROUP_SEP_MONTH = "в сентябре";
    public const GROUP_AUG_MONTH = "в августе";
    public const GROUP_MANY_MONTH = "давно";
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

    public function setupAddress(): void
    {
        $this->address .= !empty($this->flat_number) ? " - {$this->flat_number}" : "";
    }

    public function setupPhone(): void
    {
        $this->phone = !empty($this->mobile) ? $this->mobile : "";
        $this->phone .= !empty($this->home) ? empty($this->phone) ? $this->home : ", {$this->home}" : "";
    }
}