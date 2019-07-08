<?php
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
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $options = [];

    /**
     * ФИО пользователя
     * @var string
     * @ORM\Column(type="string", length=255, name="full_name")
     */
    protected $fullName;

    /**
     * Работает ли пользователь?
     * @var bool
     * @ORM\Column(type="boolean", name="on_work")
     */
    protected $onWork = false;

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

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @return int
     */
    public function getBitrixId(): ?int
    {
        return $this->bitrixId;
    }

    /**
     * @param int $bitrixId
     */
    public function setBitrixId(?int $bitrixId): void
    {
        $this->bitrixId = $bitrixId;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return bool
     */
    public function getOnWork()
    {
        return $this->onWork;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param $on_work
     * @return $this
     */
    public function setOnWork($onWork)
    {
        $this->on_work = $onWork;
        return $this;
    }

    /**
     * @param $full_name
     * @return $this
     */
    public function setFullName($fullName)
    {
        $this->full_name = $fullName;
        $this->full_name = $fullName;
        return $this;
    }

    /**
     * @param $region
     * @return $this
     */
    public function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    public function __toString()
    {
        return empty($this->getFullName())?'User':$this->getFullName();
    }

    public function getOptions()
    {
        return $this->options;
    }
    public function setOptions()
    {
        $this->options = $options;
    }
    public function removeOption($key)
    {
        unset($this->options[$key]);
    }
    public function hasOption($key)
    {
        if(array_key_exists($key, $this->options))
        {
            return true;
        }
        return false;
    }
    public function getOption($key)
    {
        return $this->options[$key];
    }
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }
}
