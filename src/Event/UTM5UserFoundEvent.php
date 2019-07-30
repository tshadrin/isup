<?php
declare(strict_types=1);

namespace App\Event;

use App\Entity\UTM5\UTM5User;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class UTM5UserEvent
 * @package App\Event
 */
class UTM5UserFoundEvent extends GenericEvent
{
    /**
     * @var UTM5User
     */
    private $user;
    /**
     * @var array
     */
    private $result;

    /**
     * UTM5UserFoundEvent constructor.
     * @param UTM5User $user
     */
    public function __construct(UTM5User $user)
    {
        parent::__construct();
        $this->user = $user;
        $result = [];
    }

    /**
     * @return UTM5User
     */
    public function getUser(): UTM5User
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addResult(string $key, string $value): void
    {
        $this->result[$key] = $value;
    }
}
