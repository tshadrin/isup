<?php

namespace App\Repository\Phone;

use Doctrine\ORM\EntityRepository;
use App\Entity\Phone\Phone;

/**
 * Class PhoneRepository
 * @package App\Repository\Phone
 */
class PhoneRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getAll(): array
    {
        $query =  $this->createQueryBuilder('p')
            ->where('p.deleted = 0')
            ->orderBy('p.number', 'ASC')
            ->getQuery();
        if(!$phones = $query->getResult()){
            throw new \DomainException("Phones not found.");
        }
        return $phones;
    }

    /**
     * @param $data
     * @return array
     */
    public function getFromAllFields($data): array
    {
        $query =  $this->createQueryBuilder('p')
            ->where('p.deleted = 0')
            ->andWhere('p.number = :data')
            ->orWhere('p.moscownumber = :data')
            ->orWhere('p.contactnumber = :data')
            ->orWhere('p.ip = :data')
            ->orWhere('LOWER(p.location) LIKE LOWER(:data1)')
            ->orWhere('LOWER(p.notes) LIKE LOWER(:data2)')
            ->orWhere('LOWER(p.name) LIKE LOWER(:data2)')
            ->orderBy('p.number', 'ASC')
            ->setParameter('data', $data)
            ->setParameter('data1',"%{$data}%")
            ->setParameter('data2', "%{$data}%")
            ->getQuery();
        if(!$phones = $query->getResult()){
            throw new \DomainException("Phones not found.");
        }
        return $phones;
    }

    /**
     * Поиск неудаленного телефона по id
     * @param $id
     * @return Phone
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getById($id): Phone
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.deleted = 0')
            ->andWhere('p.id = :id')
            ->setParameter(':id', $id)
            ->getQuery();
        if(!$phone = $query->getOneOrNullResult()) {
            throw new \DomainException("Phone not found.");
        }
        return $phone;
    }

    /**
     * Сохранение телефона
     * @param Phone $phone
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(Phone $phone)
    {
        $this->getEntityManager()->persist($phone);
    }

    public function delete(Phone $phone)
    {
        $phone->setDeleted(true);
        $this->save($phone);
    }
    /**
     * Выполнение запроса
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    public function getNew(): Phone
    {
        return new Phone();
    }
}
