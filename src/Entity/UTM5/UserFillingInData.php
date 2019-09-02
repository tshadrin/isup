<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UTM5\UserFillingInDataRepository")
 * @ORM\Table(name="user_filling_in_data")
 */
class UserFillingInData
{
    /**
     * Идентификатор комментария
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * Идентификатор пользователя в UTM5
     * @var int
     * @ORM\Column(type="integer", name="utm_id")
     */
    private $utmId;
    /**
     * Пользователь оставивший комментарий
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=false)
     */
    private $user;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUtmId(): int
    {
        return $this->utmId;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function __construct(User $user, int $utmId)
    {
        $this->date = new \DateTime();
        $this->user = $user;
        $this->utmId = $utmId;
    }
}
