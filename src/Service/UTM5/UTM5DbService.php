<?php

namespace App\Service\UTM5;

use App\Entity\UTM5\UTM5User;
use App\Repository\UTM5\UTM5UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

/**
 * Class UTM5DbService
 * @package App\Service\UTM5
 */
class UTM5DbService
{
    public const SEARCH_TYPE_ID = 'id';
    public const SEARCH_TYPE_LOGIN = 'login';
    public const SEARCH_TYPE_IP = 'ip';
    public const SEARCH_TYPE_FULLNAME = 'fullname';
    public const SEARCH_TYPE_ADDRESS = 'address';
    public const SEARCH_TYPE_PHONE = 'phone';
    public const SEARCH_TYPE_ACCOUNT = 'account';
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
     * @return \App\Collection\UTM5\UTM5UserCollection|UTM5User
     */
    public function search(string $search_value, string $search_type = self::SEARCH_TYPE_ID)
    {
        Assert::notEmpty($search_value);
        Assert::oneOf($search_type,[
            self::SEARCH_TYPE_ID,
            self::SEARCH_TYPE_LOGIN,
            self::SEARCH_TYPE_IP,
            self::SEARCH_TYPE_ADDRESS,
            self::SEARCH_TYPE_FULLNAME,
            self::SEARCH_TYPE_PHONE,
            self::SEARCH_TYPE_ACCOUNT
        ]);

        switch($search_type) {
            case self::SEARCH_TYPE_ID:
                $result = $this->UTM5UserRepository->findById((int)$search_value);
                break;
            case self::SEARCH_TYPE_LOGIN:
                $result =  $this->UTM5UserRepository->findByLogin($search_value);
                break;
            case self::SEARCH_TYPE_IP:
                Assert::ip($search_value);
                $result = $this->UTM5UserRepository->findByIP($search_value);
                break;
            case self::SEARCH_TYPE_FULLNAME:
                $result = $this->UTM5UserRepository->findByFullName($search_value);
                break;
            case self::SEARCH_TYPE_ADDRESS:
                $result = $this->UTM5UserRepository->findByAddress($search_value);
                break;
            case self::SEARCH_TYPE_PHONE:
                $result = $this->UTM5UserRepository->findByPhone($search_value);
                break;
            case self::SEARCH_TYPE_ACCOUNT:
                $result = $this->UTM5UserRepository->findByAccount($search_value);
                break;
            default:
                break;
        }

        if($result instanceof UTM5User) {
            $result->setComments($this->em->getRepository('App:UTM5\UTM5UserComment')
                ->findBy(['utmId' => $result->getId()], ['datetime' => 'DESC']));
        }
        return $result;
    }
}
