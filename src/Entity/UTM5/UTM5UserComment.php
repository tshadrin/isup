<?php
declare(strict_types=1);

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
    public function getUtmId(): int
    {
        return $this->utmId;
    }

    /**
     * @param int $utmId
     */
    public function setUtmId(int $utmId): void
    {
        $this->utmId = $utmId;
    }

    /**
     * UTM5UserComment constructor.
     * @param User $user
     * @throws \Exception
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
