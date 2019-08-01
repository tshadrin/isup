<?php
declare(strict_types=1);

namespace App\Repository\Vlan;

use App\Entity\Vlan\Vlan;
use App\Form\Vlan\DTO\Filter;
use Doctrine\ORM\EntityRepository;

/**
 * Class VlanRepository
 * @package App\Repository\Vlan
 */
class VlanRepository extends EntityRepository
{
    /**
     * @param Filter $filter
     * @return array
     */
    public function getFilteredVlans(Filter $filter): array
    {
        $queryBuilder = $this->createQueryBuilder('v');
        if ($filter->isNotEmpty()) {
            $queryBuilder->orWhere('v.number = :data')
                ->orWhere('LOWER(v.name) LIKE LOWER(:data1)')
                ->orWhere('LOWER(v.points) LIKE LOWER(:data2)')
                ->setParameter('data', $filter->value)
                ->setParameter('data1', "%{$filter->value}%")
                ->setParameter('data2', "%{$filter->value}%")
                ->getQuery();
        }
        $queryBuilder->andWhere('v.deleted = 0')
            ->orderBy('v.number', 'ASC');

        $query = $queryBuilder->getQuery();
        if (!$vlans = $query->getResult()){
            throw new \DomainException("VLans not found");
        }
        return $vlans;
    }

    /**
     * Поиск неудаленного VLAN по id
     * @param int $id
     * @return Vlan
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(int $id): Vlan
    {
        $query = $this->createQueryBuilder('v')
            ->where('v.deleted = 0')
            ->andWhere('v.id = :id')
            ->setParameter(':id', $id)
            ->getQuery();
        if (!$vlan = $query->getOneOrNullResult()) {
            throw new \DomainException("Vlan not found.");
        }
        return $vlan;
    }

    /**
     * Сохранение телефона
     * @param Vlan $phone
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(Vlan $vlan): void
    {
        $this->getEntityManager()->persist($vlan);
    }

    /**
     * @param Vlan $vlan
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(Vlan $vlan): void
    {
        $vlan->setDeleted(true);
        $this->save($vlan);
    }

    /**
     * Выполнение запроса
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return Vlan
     */
    public function getNew(): Vlan
    {
        return new Vlan();
    }
}

