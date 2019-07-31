<?php

namespace App\Service\UTM5;

use App\Entity\UTM5\UTM5User;
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
        switch($search_type) {
            case 'id':
                $result = $this->UTM5UserRepository->findById($search_value);
                break;
            case 'login':
                $result =  $this->UTM5UserRepository->findByLogin($search_value);
                break;
            case 'ip':
                $result = $this->UTM5UserRepository->findByIP($search_value);
                break;
            case 'fullname':
                $result = $this->UTM5UserRepository->findByFullName($search_value);
                break;
            case 'address':
                $result = $this->UTM5UserRepository->findByAddress($search_value);
                break;
            case 'phone':
                $result = $this->UTM5UserRepository->findByPhone($search_value);
                break;
            default:
                throw new \DomainException("Invalid search type");
        }

        if($result instanceof UTM5User) {
            $result->setComments($this->em->getRepository('App:UTM5\UTM5UserComment')
                ->findBy(['utmId' => $result->getId()], ['datetime' => 'DESC']));
        }
        return $result;
    }
}
