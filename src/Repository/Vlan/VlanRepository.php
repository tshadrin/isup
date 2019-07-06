<?php
declare(strict_types=1);

namespace App\Repository\Vlan;

use App\Entity\Vlan\Vlan;
use Doctrine\ORM\EntityRepository;

/**
 * Class VlanRepository
 * @package App\Repository\Vlan
 */
class VlanRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->createQueryBuilder('v')
                ->where('v.deleted = 0')
                ->orderBy('v.number', 'ASC')
                ->getQuery();
        if(!$vlans = $query->getResult()){
            throw new \DomainException("VLans not found.");
        }
        return $vlans;
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function findByCriteria(string $data): array
    {
        $query = $this->createQueryBuilder('v')
            ->where('v.deleted = 0')
            ->andWhere('v.number = :data')
            ->orWhere('LOWER(v.name) LIKE LOWER(:data1)')
            ->orWhere('LOWER(v.points) LIKE LOWER(:data2)')
            ->orderBy('v.number', 'ASC')
            ->setParameter('data', $data)
            ->setParameter('data1',"%{$data}%")
            ->setParameter('data2', "%{$data}%")
            ->getQuery();
        if(!$vlans = $query->getResult()){
            throw new \DomainException("VLans not found.");
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
        if(!$vlan = $query->getOneOrNullResult()) {
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

