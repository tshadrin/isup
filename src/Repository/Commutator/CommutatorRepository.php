<?php
declare(strict_types=1);

namespace App\Repository\Commutator;

use App\Entity\Commutator\Commutator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use DomainException;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommutatorRepository extends ServiceEntityRepository
{
    /** @var TranslatorInterface  */
    private $translator;

    public function __construct(ManagerRegistry $registry, TranslatorInterface $translator)
    {
        parent::__construct($registry, Commutator::class);
        $this->translator = $translator;
    }

    /**
     * @param string $ip
     * @return Commutator|null
     */
    public function getByIP(string $ip): ?Commutator
    {
        /** @var Commutator $commutator */
        if($commutator = $this->findOneBy(['ip' => $ip])) {
            return $commutator;
        }
        throw new DomainException($this->translator->trans("Switch not found by ip %ip%", ['%ip%' => $ip]));
    }
}
