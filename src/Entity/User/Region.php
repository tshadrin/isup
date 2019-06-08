<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;


/**
 * Регион пользователя
 * Class Region
 * @package App\Entity\User
 * @ORM\Entity()
 * @ORM\Table(name="regions")
 */
class Region
{
    /**
     * Идентификатор региона
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Машинное имя региона
     * @var string
     * @ORM\Column(type="string", length=30)
     */
    protected $name;

    /**
     * Описание региона
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $description;

    /**
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * @return string
     */
    public function getName() { return $this->name; }

    /**
     * @return string
     */
    public function getDescription() { return $this->description; }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id) { $this->id = $id; return $this; }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name) { $this->name = $name; return $this; }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description) { $this->description = $description; return $this; }

    public function __toString()
    {
        return empty($this->description)?'Region':$this->description;
    }
}
