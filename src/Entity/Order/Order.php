<?php
declare(strict_types=1);

namespace App\Entity\Order;

use App\Entity\Intercom\Status;
use App\Entity\User\User;
use App\Entity\UTM5\UTM5User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Order
 * @package App\Entity\Order
 * @ORM\Entity(repositoryClass="App\Repository\Order\OrderRepository")
 * @ORM\Table(name="orders")
 */
class Order
{
    /**
     * Идентификатор заявки
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * Идентификатор пользователя UTM5
     * @var int
     * @ORM\Column(type="integer", nullable=true, name="utm_id")
     */
    private $utmId;
    /**
     * Имя пользователя
     * @var string
     * @ORM\Column(type="string", length=90, name="full_name")
     */
    private $fullName;
    /**
     * Адрес пользователя
     * @var string
     * @ORM\Column(type="string", length=120)
     */
    private $address;
    /**
     * Имя сервера
     * @var string
     * @ORM\Column(type="string", length=30, nullable=true, name="server_name")
     */
    private $serverName;
    /**
     * IP пользователя
     * @var string
     * @ORM\Column(type="string", length=30, nullable=true, name="ip_address")
     */
    private $ip;
    /**
     * Комментарий к заявке
     * @var string
     * @ORM\Column(type="string", length=300)
     */
    private $comment;
    /**
     * Мобильный телефон пользователя UTM5
     * @var string
     * @ORM\Column(type="string", length=80, name="mobile_telephone")
     */
    private $phone;
    /**
     * Пользователь добавивший заявку
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;
    /**
     * Дата завершения заявки в формате unix_timestamp
     * @var int
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $completed;
    /**
     * Пользователь выполняющий заявку
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="executed", referencedColumnName="id", nullable=true)
     */
    private $executed;
    /**
     * Пользователь удаливший заявку
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="deleted_id", referencedColumnName="id", nullable=true)
     */
    private $deletedId;
    /**
     * Статус заявки
     * @var Status
     * @ORM\ManyToOne(targetEntity="App\Entity\Intercom\Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=false)
     */
    private $status;
    /**
     * @var \Datetime
     * @ORM\Column(type="datetime")
     */
    private $created;
    /**
     * Флаг удаленной заявки
     * @var bool
     * @ORM\Column(type="boolean", length=255, name="is_deleted")
     */
    private $isDeleted;
    /**
     * @var bool
     */
    private $emptyPassport;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUtmId(): ?int
    {
        return $this->utmId;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return string|null
     */
    public function getServerName(): ?string
    {
        return $this->serverName;
    }

    /**
     * @return string
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getCompleted(): int
    {
        return $this->completed;
    }

    /**
     * @return User|null
     */
    public function getExecuted(): ?User
    {
        return $this->executed;
    }

    /**
     * @return User
     */
    public function getDeletedId(): User
    {
        return $this->deletedId;
    }

    /**
     * @return Status|null
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    /**
     * @return \Datetime
     */
    public function getCreated(): \Datetime
    {
        return $this->created;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @return bool
     */
    public function isEmptyPassport(): ?bool
    {
        return $this->emptyPassport;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int $utmId
     */
    public function setUtmId(int $utmId): void
    {
        $this->utmId = $utmId;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @param string $serverName
     */
    public function setServerName(string $serverName): void
    {
        $this->serverName = $serverName;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = strip_tags($comment);
    }

    /**
     * @param string $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @param int $completed
     */
    public function setCompleted(int $completed): void
    {
        $this->completed = $completed;
    }

    /**
     * @param User $executed
     */
    public function setExecuted(User $executed): void
    {
        $this->executed = $executed;
    }

    /**
     * @param User $deletedId
     */
    public function setDeletedId(User $deletedId): void
    {
        $this->deletedId = $deletedId;
    }

    /**
     * @param Status $status
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @param \Datetime $created
     */
    public function setCreated(\Datetime $created): void
    {
        $this->created = $created;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @param bool $emptyPassport
     */
    public function setEmptyPassport(bool $emptyPassport): void
    {
        $this->emptyPassport = $emptyPassport;
    }


    public function deleteExecuted()
    {
        $this->executed = null;
    }

    /**
     * Order constructor.
     * @param array $data
     * @throws \Exception
     */
    public function __construct()
    {
        $this->isDeleted = false;
        $this->emptyPassport = false;
        $this->created = new \Datetime();
    }

    /**
     * @param UTM5User $user
     * @return Order
     * @throws \Exception
     */
    public static function createByUTM5User(UTM5User $user)
    {
        $order = new self;
        $order->setUtmId($user->getId());
        $order->setFullName($user->getFullName());
        $order->setAddress($user->getAddress());
        $ips = $user->getIps();
        $order->setIp($ips[0]);
        $order->setPhone($user->getMobilePhone());
        $routers = $user->getRouters();
        $order->setServerName($routers[0]->getName());
        return $order;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->id} - {$this->fullName}";
    }
}
