<?php

namespace App\Service\UTM5;

use App\Repository\UTM5\UTM5UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UTM5DbService
 * @package App\Service\UTM5
 */
class UTM5DbService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UTM5UserRepository
     */
    private $UTM5UserRepository;

    /**
     * UTM5DbService constructor.
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, UTM5UserRepository $UTM5UserRepository)
    {
        $this->em = $em;
        $this->UTM5UserRepository = $UTM5UserRepository;
    }

    /**
     * @param string $search_value
     * @param string $search_type
     * @return \App\Entity\UTM5\UTM5User|mixed
     */
    public function search(string $search_value, string $search_type = 'id')
    {
        if('id' === $search_type) {
            return $this->UTM5UserRepository->findById($search_value);
        }
        if('login' === $search_type) {
            return $this->UTM5UserRepository->findByLogin($search_value);
        }
        if('ip' === $search_type) {
            return $this->UTM5UserRepository->findByIP($search_value);
        }
        if('fullname' === $search_type) {
            return $this->UTM5UserRepository->findByFullName($search_value);
        }
        if('address' === $search_type) {
            return $this->UTM5UserRepository->findByAddress($search_value);
        }
    }
}
