<?php
declare(strict_types = 1);


namespace App\Form\Phone;


class Filter
{
    public $search;

    public function isNotEmpty(): bool
    {
        return !empty($this->search);
    }
}
