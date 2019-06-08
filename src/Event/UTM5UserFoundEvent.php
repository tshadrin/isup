<?php
namespace App\Event;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Templating\DelegatingEngine;
use App\Entity\UTM5\UTM5User;

/**
 * Class UTM5UserEvent
 * @package App\Event
 */
class UTM5UserFoundEvent extends GenericEvent
{
    const EVENT_NAME = 'utm5.user_found';

    /**
     * @var UTM5User
     */
    private $user;

    private $templating;

    private $result = array();

    /**
     * UTM5UserFoundEvent constructor.
     * @param UTM5User $user
     */
    public function __construct(UTM5User $user)
    {
        $this->user = $user;
    }

    /**
     * @return UTM5User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return DelegatingEngine
     */
    public function getTemplating()
    {
        return $this->templating;
    }

    /**
     *
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Добавить значение к результату события
     * @param $key
     * @param $value
     */
    public function addResult($key, $value)
    {
        $this->result[$key] = $value;
    }
}
