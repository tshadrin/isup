<?php
declare(strict_types=1);

namespace App\Service\Statistics\Actualization\Blocked;

class UserDTO
{
    public const NO_TARIFFS = "нет тарифов";
    public const ZONE1_PLUS = "зона 1 плюсы";
    public const ZONE1_ISTRINSKY_RAION = "зона 1 истринский район";
    public const ZONE2_PLUS = "зона 2 плюсы";
    public const ZONE2_CHAST_SEKTOR = "зона 2 частные дома";

    public $id;
    public $fullname;
    private $mobile;
    private $home;
    public $email;
    public $address;
    public $tariffs;
    public $zone;
    public $flat_number;
    public $phone;
    public $balance;
    private $gid900;
    private $gid902;
    private $gid912;
    private $gid913;

    public function setupAddress(): void
    {
        $this->address .= !empty($this->flat_number) ? " - {$this->flat_number}" : "";
    }

    public function setupPhone(): void
    {
        $this->phone = !empty($this->mobile) ? $this->mobile : "";
        $this->phone .= !empty($this->home) ? empty($this->phone) ? $this->home : ", {$this->home}" : "";
    }

    public function setupZone(): void
    {
        if (!is_null($this->gid900)) {
            $this->zone = self::ZONE1_PLUS;
        }
        if (!is_null($this->gid902)) {
            $this->zone = self::ZONE1_ISTRINSKY_RAION;
        }
        if (!is_null($this->gid912)) {
            $this->zone = self::ZONE2_PLUS;
        }
        if (!is_null($this->gid913)) {
            $this->zone = self::ZONE2_CHAST_SEKTOR;
        }
    }
}