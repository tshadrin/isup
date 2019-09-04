<?php
declare(strict_types=1);

namespace App\Entity\Statistics;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Statistics\OnlineUsersRepository")
 * @ORM\Table(name="online_users_statistics")
 */
class OnlineUsers
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $date;
    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $server;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $count;

    public function __construct(\DateTime $date, string $server, int $count)
    {
        $this->date = $date;
        $this->server = $server;
        $this->count = $count;
    }
}
