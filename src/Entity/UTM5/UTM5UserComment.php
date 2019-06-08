<?php

namespace App\Entity\UTM5;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Комментарий к пользователю UTM5
 * Class UserComment
 * @package App\Entity\UTM5
 * @ORM\Entity()
 */
class UTM5UserComment extends Comment
{
    /**
     * Идентификатор пользователя в UTM5
     * @var int
     * @ORM\Column(type="integer", name="utm_id")
     */
    protected $utmId;

    /**
     * @return int
     */
    public function getUtmId() { return $this->utmId; }

    /**
     * @param int $utmId
     * @return $this
     */
    public function setUtmId(int $utmId) { $this->utmId = $utmId; return $this; }

    /**
     * UTM5UserComment constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
