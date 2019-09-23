<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UTM5\TypicalCallRepository")
 * @ORM\Table(name="typical_calls")
 */
class TypicalCall
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="text", length=500)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, name="short_cut")
     */
    private $shortCut;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

    public function __construct(string $description, string $shortCut)
    {
        $this->description = $description;
        $this->shortCut = $shortCut;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getShortCut(): ?string
    {
        return $this->shortCut;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param string $shortCut
     */
    public function setShortCut(string $shortCut): void
    {
        $this->shortCut = $shortCut;
    }

    /**
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function __toString()
    {
        return $this->shortCut;
    }
}