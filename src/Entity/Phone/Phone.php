<?php
declare(strict_types=1);

namespace App\Entity\Phone;

use Symfony\Component\Validator\Constraints as Assert;
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNumber(): ?string
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
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getName(): ?string
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
    public function getIp(): ?string
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
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    /**
     * @param string|null $moscownumber
     */
    public function setMoscownumber(?string $moscownumber): void
    {
        $this->moscownumber = $moscownumber;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string|null $contactnumber
     */
    public function setContactnumber(?string $contactnumber): void
    {
        $this->contactnumber = $contactnumber;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * Phone конструктор.
     * При создании нового объекта генерируются
     * логин и пароль.
     */
    public function __construct()
    {
        $this->setLogin('admin');
        $this->setPassword(substr(md5(uniqid((string)rand(),true)),7,8));
        $this->setDeleted(false);
    }
}
