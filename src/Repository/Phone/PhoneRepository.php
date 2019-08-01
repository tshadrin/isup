<?php
declare(strict_types=1);

namespace App\Repository\Phone;

use App\Form\Phone\DTO\Filter;
use App\Entity\Phone\Phone;
use Doctrine\ORM\EntityRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PhoneRepository
 * @package App\Repository\Phone
 */
class PhoneRepository extends EntityRepository
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

    /**
     * @param Filter $filter
     * @return array
     */
    public function getFilteredPhones(Filter $filter): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        if ($filter->isNotEmpty()) {
            $queryBuilder->orWhere('p.number = :data')
                ->orWhere('p.moscownumber = :data')
                ->orWhere('p.contactnumber = :data')
                ->orWhere('p.ip = :data')
                ->orWhere('LOWER(p.location) LIKE LOWER(:data1)')
                ->orWhere('LOWER(p.notes) LIKE LOWER(:data2)')
                ->orWhere('LOWER(p.name) LIKE LOWER(:data2)')
                ->setParameter('data', $filter->value)
                ->setParameter('data1', "%{$filter->value}%")
                ->setParameter('data2', "%{$filter->value}%");
        }
        $queryBuilder->andwhere('p.deleted = 0')
            ->orderBy('p.number', 'ASC');

        $query = $queryBuilder->getQuery();
        if (!$phones = $query->getResult()){
            throw new \DomainException($this->translator->trans("Phones not found"));
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
        if (!$phone = $query->getOneOrNullResult()) {
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
