<?php
declare(strict_types = 1);


namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class Rows
{
    /**
     * @var int
     * @Assert\NotNull()
     * @Assert\GreaterThanOrEqual(value="0")
     */
    public $value;
}
