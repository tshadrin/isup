<?php
declare(strict_types=1);

namespace App\Entity\Intercom;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Task
 * @package App\Entity\Intercom
 * @ORM\Entity(repositoryClass="App\Repository\Intercom\TaskRepository")
 * @ORM\Table(name="intercom_tasks")
 */
class Task
{
    const STATUS_COMPLETE = 'complete';

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
    private $phone;
    /**
     * ФИО Клиента
     * @var string
     * @ORM\Column(type="string", length=255, name="full_name")
     */
    private $fullname;
    /**
     * Адрес клиента
     * @var string
     * @ORM\Column(type="string", length=1024)
     */
    private $address;
    /**
     * Описание задачи
     * @var string
     * @ORM\Column(type="string", length=4096, nullable=true)
     * @Assert\Length(max="4096")
     */
    private $description;
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
     * @var \DateTime
     * @ORM\Column(type="datetime", length=100, nullable=true)
     * @Assert\DateTime()
     */
    private $completed;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": false})
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
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    /**
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
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
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @return \DateTime
     */
    public function getCompleted(): ?\DateTime
    {
        return $this->completed;
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
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @param string $fullname
     */
    public function setFullname(string $fullname): void
    {
        $this->fullname = $fullname;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @param Status $status
     * @throws \Exception
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
        if(self::STATUS_COMPLETE === $this->status->getName()) {
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
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @param \DateTime $completed
     */
    public function setCompleted(\DateTime $completed): void
    {
        $this->completed = $completed;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFullname();
    }

    /**
     * Task constructor.
     * @param User $user
     * @throws \Exception
     */
    public function __construct(User $user)
    {
        $this->setCreated(new \DateTime());
        $this->setUser($user);
        $this->setDeleted(false);
    }
}
