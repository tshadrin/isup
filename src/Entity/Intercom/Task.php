<?php

namespace App\Entity\Intercom;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Task
 * @package App\Entity\Intercom
 * @ORM\Entity(repositoryClass="App\Repository\Intercom\TaskRepository")
 * @ORM\Table(name="intercom_tasks")
 */
class Task
{
    /**
     * Идентификатор задачи
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * Контактный телефон
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $phone = '';
    /**
     * ФИО Клиента
     * @var string
     * @ORM\Column(type="string", length=255, name="full_name")
     */
    private $fullname = '';
    /**
     * Адрес клиента
     * @var string
     * @ORM\Column(type="string", length=1024)
     */
    private $address = '';
    /**
     * Описание задачи
     * @var string
     * @ORM\Column(type="string", length=4096)
     * @Assert\Length(max="4096")
     */
    private $description = '';
    /**
     * Оператор создавший задачу
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=false)
     */
    private $user;
    /**
     * Статус задачи
     * @var Status
     * @ORM\ManyToOne(targetEntity="App\Entity\Intercom\Status")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=false)
     */
    private $status;
    /**
     * Тип задачи(пока узел или абонент, по умолчанию - абонент)
     * @var Type
     * @ORM\ManyToOne(targetEntity="App\Entity\Intercom\Type")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=false)
     */
    private $type;
    /**
     * Дата создания задачи
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $created;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $deleted = false;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", length=100, nullable=true)
     * @Assert\DateTime()
     */
    private $completed;

    /**
     * @return string
     */

    /**
     * @return mixed
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param $completed
     */
    public function setCompleted($completed): void
    {
        $this->completed = $completed;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getFullname(): string
    {
        return $this->fullname;
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
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return Status
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    /**
     * @return Type
     */
    public function getType(): ?Type
    {
        return $this->type;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created): void
    {
        $this->created = $created;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @param string $fullname
     */
    public function setFullname(string $fullname): void
    {
        $this->fullname = $fullname;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @param Status $status
     * @throws \Exception
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
        if('complete' == $this->status->getName()) {
            $this->completed = new \DateTime();
        }
    }

    /**
     * @param Type $type
     */
    public function setType(Type $type): void
    {
        $this->type = $type;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @throws \Exception
     */
    public function onPrePersistSetCreated()
    {
        if(!isset($this->description)) {
            $this->setCreated(new \DateTime());
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->getFullname()}";
    }

    /**
     * Task constructor.
     * @param User $user
     * @throws \Exception
     */
    public function __construct(User $user)
    {
        $this->setCreated(new \DateTime());
        $this->user = $user;
    }
}
