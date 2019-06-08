<?php

namespace App\Repository\Commutator;
use Doctrine\ORM\EntityRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommutatorRepository extends EntityRepository
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getByIP(string $ip)
    {
        if($commutator = $this->findOneBy(['ip' => $ip])) {
            return $commutator;
        }
        throw new \DomainException($this->translator->trans("Switch not found by ip %ip%", ['%ip%' => $ip]));
    }
}
