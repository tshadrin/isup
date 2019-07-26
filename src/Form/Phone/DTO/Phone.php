<?php
declare(strict_types = 1);


namespace App\Form\Phone\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Phone
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $number;
    /**
     * @var string
     */
    public $moscownumber;
    /**
     * @var string
     */
    public $location;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $contactnumber;
    public $ip;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $login;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="8" max="12")
     */
    public $password;
    public $notes;
}