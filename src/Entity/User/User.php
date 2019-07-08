<?php
declare(strict_types=1);

namespace App\Entity\User;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @package App\Entity\User
 * @ORM\Entity()
 * @ORM\Table(name="userrs")
 */
class User extends BaseUser
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * ФИО пользователя
     * @var string
     * @ORM\Column(type="string", length=255, name="full_name")
     */
    protected $fullName;

    /**
     * Регион работы пользователя
     * @var Region
     * @ORM\ManyToOne(targetEntity="App\Entity\User\Region")
     * @ORM\JoinColumn(name="region", referencedColumnName="id", nullable=false)
     */
    protected $region;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true, name="bitrix_id", nullable=true)
     */
    protected $bitrixId;

    /**
     * Работает ли пользователь?
     * @var bool
     * @ORM\Column(type="boolean", name="on_work")
     */
    protected $onWork;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $options = [];

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @return int
     */
    public function getBitrixId(): ?int
    {
        return $this->bitrixId;
    }

    /**
     * @return Region|null
     */
    public function getRegion(): ?Region
    {
        return $this->region;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isOnWork(): bool
    {
        return $this->onWork;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @param int $bitrixId
     */
    public function setBitrixId(?int $bitrixId): void
    {
        $this->bitrixId = $bitrixId;
    }

    /**
     * @param Region $region
     */
    public function setRegion(Region $region): void
    {
        $this->region = $region;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @param bool $onWork
     */
    public function setOnWork(bool $onWork): void
    {
        $this->onWork = $onWork;
    }

    /**
     * @param $key
     */
    public function removeOption(string $key): void
    {
        unset($this->options[$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options)?true:false;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getOption($key)
    {
        return $this->hasOption($key)?$this->options[$key]:null;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function setOption(string $key, $value): void
    {
        $this->options[$key] = $value;
    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->onWork = false;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return empty($this->getFullName())?'User':$this->getFullName();
    }
}
