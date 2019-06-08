<?php

namespace App\Entity\Phone;

use Symfony\Component\Validator\Constraints as Assert;
use Webmozart\Assert\Assert as WAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Телефон
 * Class Phone
 * @package App\Entity\Phone
 * @ORM\Entity(repositoryClass="App\Repository\Phone\PhoneRepository")
 * @ORM\Table(name="phones")
 */
class Phone
{
    /**
     * Идентификатор
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Номер
     * @var string
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^6\-\d{2}\-\d{2}/")
     * @ORM\Column(type="string", length=255)
     */
    private $number;

    /**
     * Московский номер
     * @var string|null
     * @Assert\Regex(pattern="/^551\-\d{2}\-\d{2}/")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moscownumber;

    /**
     * Адрес установки
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="200")
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * ФИО Клиента
     * @var string
     * @Assert\Length(max="200")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * Контактный номер
     * @var string|null
     * @Assert\Regex(pattern="/^8\(\d{3}\)\d{3}\-\d{2}\-\d{2}/")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contactnumber;

    /**
     * IP VoIP адаптера
     * @var string
     * @Assert\NotBlank()
     * @Assert\Ip()
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * Логин VoIP адаптера
     * @var string
     * @Assert\Regex(pattern="/^[a-zA-Z]+/")
     * @ORM\Column(type="string", length=255)
     */
    private $login;

    /**
     * Пароль VoIP адаптера
     * @var string
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[a-zA-Z0-9]+/")
     * @Assert\Length(min="8")
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * Заметки
     * @var string|null
     * @Assert\Length(max="200")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notes;

    /**
     * Удален ли телефон?
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $deleted;


    /**
     * Phone конструктор.
     * При создании нового объекта генерируются
     * логин и пароль.
     */
    public function __construct()
    {
        $this->number = '';
        $this->location = '';
        $this->name = '';
        $this->ip = '';
        $this->setLogin('admin');
        $this->setPassword(substr(md5(uniqid(rand(),true)),7,8));
        $this->setDeleted(false);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return string|null
     */
    public function getMoscownumber(): ?string
    {
        return $this->moscownumber;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getContactnumber(): ?string
    {
        return $this->contactnumber;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param $number
     * @return $this
     */
    public function setNumber($number)
    {
        WAssert::notEmpty($number);
        $this->number = $number; return $this;
    }

    /**
     * @param $moscownumber
     * @return $this
     */
    public function setMoscownumber($moscownumber)
    {
        $this->moscownumber = $moscownumber; return $this;
    }

    /**
     * @param $location
     * @return $this
     */
    public function setLocation($location)
    {
        WAssert::notEmpty($location);
        $this->location = $location; return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        WAssert::notEmpty($name);
        $this->name = $name; return $this;
    }

    /**
     * @param $contactnumber
     * @return $this
     */
    public function setContactnumber($contactnumber)
    {
        $this->contactnumber = $contactnumber; return $this;
    }

    /**
     * @param $ip
     * @return $this
     */
    public function setIp($ip)
    {
        WAssert::notEmpty($ip);
        $this->ip = $ip; return $this;
    }

    /**
     * @param $login
     * @return $this
     */
    public function setLogin($login)
    {
        $this->login = $login; return $this;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        WAssert::notEmpty($password);
        $this->password = $password; return $this;
    }

    /**
     * @param string $notes
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->notes = $notes; return $this;
    }

    /**
     * @param bool $deleted
     * @return $this
     */
    public function setDeleted($deleted)
    {
        WAssert::boolean($deleted);
        $this->deleted = $deleted; return $this;
    }
}
