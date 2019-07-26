<?php
declare(strict_types = 1);


namespace App\Form\Vlan\DTO;


class Filter
{
    /**
     * @var string
     */
    public $value;

    /**
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return !empty($this->value);
    }
}
