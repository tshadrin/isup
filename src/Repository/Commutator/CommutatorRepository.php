<?php
declare(strict_types=1);

namespace App\Repository\Commutator;

use App\Entity\Commutator\Commutator;
use Doctrine\ORM\EntityRepository;
use DomainException;
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
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param string $ip
     * @return Commutator|null
     */
    public function getByIP(string $ip): ?Commutator
    {
        if($commutator = $this->findOneBy(['ip' => $ip])) {
            return $commutator;
        }
        throw new DomainException($this->translator->trans("Switch not found by ip %ip%", ['%ip%' => $ip]));
    }
}
