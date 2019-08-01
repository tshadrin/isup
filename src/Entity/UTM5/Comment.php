<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use App\Entity\User\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

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
     * @var string
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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDatetime(): string
    {
        return $this->datetime;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return User
     */
    public function getUserId(): User
    {
        return $this->userId;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param User $userId
     */
    public function setUserId(User $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @param string $datetime
     */
    public function setDatetime(string $datetime): void
    {
        $this->datetime = $datetime;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * Comment constructor.
     * @param User $userId
     * @throws Exception
     */
    public function __construct(User $userId)
    {
        $this->userId = $userId;
        $date = new DateTime();
        $this->setDatetime($date->format("U"));
    }
}
