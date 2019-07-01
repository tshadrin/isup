<?php

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
     * @ORM\Column(type="bigint", nullable=true, name="utm_id")
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
    private $ipAddress;

    /**
     * Комментарий к заявке
     * @var string
     * @ORM\Column(type="string", length=300)
     */
    private $comment;

    /**
     * Флаг удаленной заявки
     * @var bool
     * @ORM\Column(type="boolean", length=255, name="is_deleted")
     */
    private $isDeleted = false;

    /**
     * Мобильный телефон пользователя UTM5
     * @var string
     * @ORM\Column(type="string", length=80, name="mobile_telephone")
     */
    private $mobileTelephone;

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

    private $emptyPassport = false;

    public function __construct(array $data = array())
    {
        if (count($data)) {
            $this->utmId = $data['user_id'];
            $this->fullName = $data['full_name'];
            $this->address = $data['address'];
            $this->serverName = $data['server_name'];
            $this->ipAddress = $data['ip_address'];
            $this->user = $data['user'];
            $this->mobileTelephone = $data['mobile'];
            $this->comment = $data['comment'];
            $this->status=$data['status'];
        }
        $this->isDeleted = false;
        $this->created = new \Datetime();
        $this->emptyPassport = false;
    }

    /**
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * @return int
     */
    public function getUtmId()
    {
        return $this->utmId;
    }

    /**
     * @return string
     */
    public function getFullName() { return $this->fullName; }

    /**
     * @return string
     */
    public function getAddress() { return $this->address; }

    /**
     * @return string
     */
    public function getServerName() { return $this->serverName; }

    /**
     * @return string
     */
    public function getIpAddress() { return $this->ipAddress; }

    /**

     * @return bool
     */
    public function getIsDeleted() { return $this->isDeleted; }

    /**
     * @return string
     */
    public function getComment() { return $this->comment; }

    /**
     * @return User|mixed
     */
    public function getUser() { return $this->user; }

    /**
     * @return int
     */
    public function getCompleted() { return $this->completed; }

    /**
     * @return User
     */
    public function getExecuted() { return $this->executed; }

    /**
     * @return User
     */
    public function getDeletedId() { return $this->deletedId; }

    /**
     * @return string
     */
    public function getPhone() { return $this->mobileTelephone; }

    /**
     * @return Status
     */
    public function getStatus() { return $this->status; }

    /**
     * @param $comment
     * @return $this
     */
    public function setComment($comment) { $this->comment = strip_tags($comment); return $this; }

    /**
     * @param User $executed
     * @return $this
     */
    public function setExecuted(User $executed) { $this->executed = $executed; return $this; }

    /**
     * @param $isDeleted
     * @return $this
     */
    public function setIsDeleted($isDeleted) { $this->isDeleted = $isDeleted; return $this; }

    /**
     * @param $completed
     * @return $this
     */
    public function setCompleted($completed) { $this->completed = $completed; return $this; }

    /**
     * @param User $deleted
     * @return $this
     */
    public function setDeletedId(User $deleted) { $this->deletedId = $deleted; return $this; }

    /**
     * @return mixed|string
     */
    public function getMobileTelephone()
    {
        return $this->mobileTelephone;
    }
    /**
     * @param Status $status
     * @return $this
     */
    public function setStatus(Status $status) { $this->status = $status; return $this; }

    public function deleteExecuted()
    {
        $this->executed = null;
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
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @param string $mobileTelephone
     */
    public function setMobileTelephone(string $mobileTelephone): void
    {
        $this->mobileTelephone = $mobileTelephone;
    }

    /**
     * @param string $serverName
     */
    public function setServerName(string $serverName): void
    {
        $this->serverName = $serverName;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return \Datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \Datetime $created
     */
    public function setCreated(\Datetime $created)
    {
        $this->created = $created;
    }

    public static function createByUTM5User(UTM5User $user)
    {
        $order = new self;
        $order->setUtmId($user->getId());
        $order->setFullName($user->getFullName());
        $order->setAddress($user->getAddress());
        $ips = $user->getIps();
        $order->setIpAddress($ips[0]);
        if(!is_null($phone = $user->getMobilePhone())) {
            $order->setMobileTelephone($phone);
        }
        $routers = $user->getRouters();
        $order->setServerName($routers[0]->getName());
        return $order;
    }
    public function __toString()
    {
        return "{$this->id} - {$this->fullName}";
    }

    /**
     * @return bool
     */
    public function isEmptyPassport(): bool
    {
        return $this->emptyPassport;
    }

    /**
     * @param bool $emptyPassport
     */
    public function setEmptyPassport(bool $emptyPassport): void
    {
        $this->emptyPassport = $emptyPassport;
    }
}
