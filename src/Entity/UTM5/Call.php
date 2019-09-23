<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UTM5\CallRepository")
 * @ORM\Table(name="calls")
 */
class Call
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;
    /**
     * Пользователь добавивший заявку
     * @var TypicalCall
     * @ORM\ManyToOne(targetEntity="App\Entity\UTM5\TypicalCall")
     * @ORM\JoinColumn(name="typical_call_id", referencedColumnName="id", nullable=false)
     */
    private $typicalCall;
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $utm5Id;
    /**
     * Пользователь добавивший заявку
     * @var TypicalCall
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id", nullable=false)
     */
    private $operator;

    public function __construct(\DateTimeImmutable $date, TypicalCall $typicalCall, ?int $utm5Id, User $operator)
    {
        $this->date = $date;
        $this->typicalCall = $typicalCall;
        $this->utm5Id = $utm5Id;
        $this->operator = $operator;
    }
}