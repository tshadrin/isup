<?php

namespace App\Entity\UTM5;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Комментарий, базовый класс
 * Class Comment
 * @package Isup\Entity
 * @ORM\Entity()
 * @ORM\Table(name="comments")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=5)
 * @ORM\DiscriminatorMap({"user": "UTM5UserComment"})
 */
abstract class Comment
{
    /**
     * Идентификатор комментария
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Дата в формате unix_timestamp
     * @var int
     * @ORM\Column(type="bigint", length=100)
     */
    protected $datetime;

    /**
     * Тело комментария
     * @var string
     * @ORM\Column(type="string", length=300)
     */
    protected $comment;

    /**
     * Пользователь оставивший комментарий
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $userId;

    /**
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * @return int
     */
    public function getDatetime() { return $this->datetime; }

    /**
     * @return string
     */
    public function getComment() { return $this->comment; }

    /**
     * @return User
     */
    public function getUserId() { return $this->userId; }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id) { $this->id = $id; return $this; }

    /**
     * @param $userId
     * @return $this
     */
    public function setUserId($user_id) { $this->userId = $userId; return $this; }

    /**
     * @param $datetime
     * @return $this
     */
    public function setDatetime($datetime) { $this->datetime = $datetime; return $this; }

    /**
     * @param $comment
     * @return $this
     */
    public function setComment($comment) { $this->comment = $comment; return $this; }

    public function __construct(User $userId)
    {
        $this->userId = $userId;
        $date = new \DateTime();
        $this->datetime = $date->format("U");
    }
}
