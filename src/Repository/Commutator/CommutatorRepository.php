<?php
declare(strict_types=1);

namespace App\Repository\Commutator;

use App\Entity\Commutator\Commutator;
use App\Repository\SaveAndFlush;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommutatorRepository extends ServiceEntityRepository
{
    use SaveAndFlush;

    /** @var TranslatorInterface  */
    private $translator;

    public function __construct(ManagerRegistry $registry, TranslatorInterface $translator)
    {
        parent::__construct($registry, Commutator::class);
        $this->translator = $translator;
    }

    public function getByIP(string $ip): ?Commutator
    {
        /** @var Commutator $commutator */
        if($commutator = $this->findOneBy(['ip' => $ip])) {
            return $commutator;
        }
        throw new \DomainException($this->translator->trans("Switch not found by ip %ip%", ['%ip%' => $ip]));
    }
}
