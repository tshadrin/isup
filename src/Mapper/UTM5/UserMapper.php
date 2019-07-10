<?php
declare(strict_types=1);

namespace App\Mapper\UTM5;

use App\Collection\UTM5\{ PaymentCollection, ServiceCollection, TariffCollection, UTM5UserCollection };
use App\Entity\UTM5 as Entity;
use App\Entity\UTM5\PromisedPayment;
use App\Repository\UTM5\{ HouseRepository, GroupRepository, PassportRepository, PaymentRepository,
    PromisedPaymentRepository, RouterRepository, ServiceRepository, TariffRepository };
use Symfony\Contracts\Translation\TranslatorInterface;

class UserMapper
{
    /**
     * @var UserPreparer
     */
    private $userPreparer;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var HouseRepository
     */
    private $houseRepository;
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var RouterRepository
     */
    private $routerRepository;
    /**
     * @var ServiceRepository
     */
    private $serviceRepository;
    /**
     * @var TariffRepository
     */
    private $tariffRepository;
    /**
     * @var PromisedPaymentRepository
     */
    private $promisedPaymentRepository;
    /**
     * @var PaymentRepository
     */
    private $paymentRepository;
    /**
     * @var PassportRepository
     */
    private $passportRepository;

    /**
     * UserMapper constructor.
     * @param UserPreparer $userPreparer
     * @param TranslatorInterface $translator
     * @param HouseRepository $houseRepository
     * @param GroupRepository $groupRepository
     * @param RouterRepository $routerRepository
     * @param ServiceRepository $serviceRepository
     * @param TariffRepository $tariffRepository
     * @param PromisedPaymentRepository $promisedPaymentRepository
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(UserPreparer $userPreparer, TranslatorInterface $translator,
                                HouseRepository $houseRepository, GroupRepository $groupRepository,
                                RouterRepository $routerRepository, ServiceRepository $serviceRepository,
                                TariffRepository $tariffRepository, PromisedPaymentRepository $promisedPaymentRepository,
                                PaymentRepository $paymentRepository, PassportRepository $passportRepository)
    {
        $this->userPreparer = $userPreparer;
        $this->translator = $translator;
        $this->houseRepository = $houseRepository;
        $this->groupRepository = $groupRepository;
        $this->routerRepository = $routerRepository;
        $this->serviceRepository = $serviceRepository;
        $this->tariffRepository = $tariffRepository;
        $this->promisedPaymentRepository = $promisedPaymentRepository;
        $this->paymentRepository = $paymentRepository;
        $this->passportRepository = $passportRepository;
    }

    /**
     * Поиск по id
     * @param int $id
     * @return Entity\UTM5User
     */
    public function getUserById(int $id): Entity\UTM5User
    {
        try {
            $stmt = $this->userPreparer->getUserDataByIdStmt();
            $stmt->execute([':id' => $id]);
            if(1 === $stmt->rowCount()) {
                $data = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $this->UTM5UserInit($data);
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("User search error: %message%", ['%message%' => $e->getMessage()]));
        }
        throw new \DomainException($this->translator->trans("User with id %id% is not found", ['%id%' => $id]));
    }

    /**
     * Поиск по account
     * @param int $account
     * @return Entity\UTM5User
     */
    public function getUserByAccount(int $account): Entity\UTM5User
    {
        try {
            $stmt = $this->userPreparer->getUserDataByAccountStmt();
            $stmt->execute([':account' => $account]);
            if(1 === $stmt->rowCount()) {
                $data = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $this->UTM5UserInit($data);
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("User search error: %message%", ['%message%' => $e->getMessage()]));
        }
        throw new \DomainException($this->translator->trans("User with account %account% is not found", ['%account%' => $account]));
    }

    /**
     * Поиск по логину
     * @param string $login
     * @return Entity\UTM5User
     */
    public function getUserByLogin(string $login): Entity\UTM5User
    {
        try {
            $stmt = $this->userPreparer->getUserDataByLoginStmt();
            $stmt->execute([':login' => $login]);
            if(1 === $stmt->rowCount()) {
                $data = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $this->UTM5UserInit($data);
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("User search error: %message%", ['%message%' => $e->getMessage()]));
        }
        throw new \DomainException($this->translator->trans("User with login %login% is not found", ['%login%' => $login]));
    }

    /**
     * Поиск по IP адресу
     * @param string $ip
     * @return Entity\UTM5User
     */
    public function getUserByIP(string $ip): Entity\UTM5User
    {
        $ip_long = ip2long($ip);
        $ip_long = ($ip_long > 2147483647)?-2147483648+($ip_long-2147483648):$ip_long;
        try {
            $stmt = $this->userPreparer->getUserDataByIPStmt();
            $stmt->execute([':ip' => $ip_long]);
            if(1 == $stmt->rowCount()) {
                $data = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $this->UTM5UserInit($data);
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("User search error: %message%", ['%message%' => $e->getMessage()]));
        }
        throw new \DomainException($this->translator->trans("User with IP %ip% is not found", ['%ip%' => $ip]));
    }

    public function getUsersByFullname(string $full_name)
    {
        try {
            $stmt = $this->userPreparer->getUserDataByFullnameStmt();
            $stmt->execute([':full_name' => "%{$full_name}%"]);
            if(1 === ($count = $stmt->rowCount())) {
                $data = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $this->UTM5UserInit($data);
            }
            if($count > 1) {
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $users = new UTM5UserCollection();
                foreach($data as $row) {
                    $users->add($this->UTM5UserInitPartial($row));
                }
                return $users;
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("User search error: %message%", ['%message%' => $e->getMessage()]));
        }
        throw new \DomainException(
            $this->translator->trans("User search with fullname %full_name% error. Found %count% records",
                ['%full_name%' => $full_name, '%count%' => $count]));
    }

    public function getUsersByAddress(string $address)
    {
        try {
            $stmt = $this->userPreparer->getUserDataByAddressStmt();
            $stmt->execute([':address' => "%{$address}%"]);
            if(1 === ($count = $stmt->rowCount())) {
                $data = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $this->UTM5UserInit($data);
            }
            if($count > 1) {
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $users = new UTM5UserCollection();
                foreach($data as $row) {
                    $users->add($this->UTM5UserInitPartial($row));
                }
                return $users;
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("User search error: %message%", ['%message%' => $e->getMessage()]));
        }
        throw new \DomainException(
            $this->translator->trans("User search with address %address% error. Found %count% records",
                ['%address%' => $address, '%count%' => $count]));
    }

    // Дополнительная информация
    /**
     * Получение списка IP адресов пользователя.
     * IP адреса для пользователя могут быть не назначены
     * @param int $user_id
     * @return array|null
     */
    public function getIPSettings(int $user_id): ?array
    {
        try {
            $stmt = $this->userPreparer->getUserIpsStmt();
            $stmt->execute([':id' => $user_id]);
            if($stmt->rowCount() > 0)
                return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("User IPs search error: %message%", ['%message%' => $e->getMessage()]));
        }
        return null;
    }

    /**
     * Получение списка IP адресов пользователя.
     * IP адреса для пользователя могут быть не назначены
     * @param int $user_id
     * @return array|null
     */
    public function getIP6Settings(int $user_id): ?array
    {
        try {
            $stmt = $this->userPreparer->getUserIps6Stmt();
            $stmt->execute([':id' => $user_id]);
            if($stmt->rowCount() > 0)
                return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("User IPs search error: %message%", ['%message%' => $e->getMessage()]));
        }
        return null;
    }

    // ADDITIONAL FIELDS
    /**
     * @param int $user_id
     * @return string|null
     */
    public function getLifestreamLogin(int $user_id): ?string
    {
        try{
            $stmt = $this->userPreparer->getLifestreamLoginStmt();
            $stmt->execute([':user_id' => $user_id]);
            if(1 === $stmt->rowCount()) {
                return $stmt->fetch(\PDO::FETCH_COLUMN);
            }
            if($stmt->rowCount() > 1) {
                throw new \DomainException($this->translator->trans("Too many results on lifesteam query"));
            }
            return null;
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Routers data query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function getRemindMe(int $user_id): bool
    {
        try {
            $stmt = $this->userPreparer->getRemindMeStmt();
            $stmt->execute([':user_id' => $user_id]);
            if(1 === $stmt->rowCount()) {
                return (bool)$stmt->fetch(\PDO::FETCH_COLUMN);
            }
            if($stmt->rowCount() > 1) {
                throw new \DomainException($this->translator->trans("Too many results on remind me query"));
            }
            return false;
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Get remind me query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public function isUserPassport(int $user_id): bool
    {
        try{
            $stmt = $this->userPreparer->getUserPassportStmt();
            $stmt->execute([':id' => $user_id]);
            if(1 === $stmt->rowCount()) {
                $result = $stmt->fetch(\PDO::FETCH_COLUMN);
                return empty($result);
            } else {
                throw new \DomainException($this->translator->trans("User with id %id% is not found", ['%id%' => $user_id]));
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Check user passport query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }

    /**
     * Поиск информации о блокировках пользователя
     * @param int $account
     * @return int
     */
    public function getBlock(int $account): int
    {
        try{
            $stmt = $this->userPreparer->getBlockByAccountStmt();
            $stmt->execute([':basic_account' => $account]);
            if(0 === $stmt->rowCount()) {
                return 0;
            }
            if (1 === $stmt->rowCount()) {
                return (int)$stmt->fetch(\PDO::FETCH_COLUMN);
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Check user passport query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }

    /**
     * @param array $data
     * @return Entity\UTM5User
     */
    public function UTM5UserInitPartial(array $data): Entity\UTM5User
    {
        $user = new Entity\UTM5User();
        $user->setId($data['id']);
        $user->setLogin($data['login']);
        $user->setFullName($data['full_name']);
        if(!empty($data['actual_address'])) {
            $user->setAddress($data['actual_address']);
        }
        $user->setFlatNumber($data['flat_number']);
        if(0 !== (int)$data['house_id']) {
            $user->setHouse($this->houseRepository->findOneById($data['house_id']));
        }
        return $user;
    }

    /**
     * @param array $data
     * @return Entity\UTM5User
     */
    public function UTM5UserInit(array $data): Entity\UTM5User
    {
        $user = new Entity\UTM5User();
        $user->setId((int)$data['id']);
        $user->setLogin($data['login']);
        if(!empty($data['email'])) {
            $user->setEmail($data['email']);
        }
        $user->setPassword($data['password']);
        $user->setAccount((int)$data['account']);
        $user->setFullName($data['full_name']);
        $user->setPassport($data['passport']);
        $user->setFlatNumber($data['flat_number']);
        if(!empty($data['actual_address'])) {
            $user->setAddress($data['actual_address']);
        }
        if(!empty($data['juridical_address'])) {
            $user->setJuridicalAddress($data['juridical_address']);
        }
        $user->setBalance((float)$data['balance']);
        $user->setInternetStatus((bool)$data['int_status']);
        if(!empty($data['mobile_telephone'])) {
            $user->setMobilePhone($data['mobile_telephone']);
        }
        if(!empty($data['home_telephone'])) {
            $user->setHomePhone($data['home_telephone']);
        }
        if(!empty($data['work_telephone'])) {
            $user->setWorkPhone($data['work_telephone']);
        }
        if(!empty($data['utm5_comments'])) {
            $user->setUTM5Comments($data['utm5_comments']);
        }
        $user->setCredit((float)$data['credit']);
        $user->setCreated(\DateTimeImmutable::createFromFormat('U', $data['created']));
        if(!is_null($ips =$this->getIPSettings($user->getId()))) {
            $user->setIps($ips);
        }
        if(!is_null($ips6 =$this->getIP6Settings($user->getId()))) {
            $user->setIps6($ips6);
        }
        $user->setRemindMe($this->getRemindMe($user->getId()));
        if(!is_null($lifestreamLogin = $this->getLifestreamLogin($user->getId()))) {
            $user->setLifestreamLogin($lifestreamLogin);
        }
        $user->setBlock($this->getBlock($user->getAccount()));
        $user->setGroups($this->groupRepository->findByUserId($user->getId()));
        if(0 !== (int)$data['house_id']) {
            $user->setHouse($this->houseRepository->findOneById((int)$data['house_id']));
        }
        if(!is_null($routers = $this->routerRepository->findByUserId($user->getId()))) {
            $user->setRouters($routers);
        }
        if(($services = $this->serviceRepository->findByAccount($user->getAccount())) instanceof ServiceCollection) {
            $user->setServices($services);
        }
        if(($tariffs = $this->tariffRepository->findTariffByAccount($user->getAccount())) instanceof TariffCollection) {
            $user->setTariffs($tariffs);
        }
        if(($promisedPayment = $this->promisedPaymentRepository->findByAccount($user->getAccount())) instanceof PromisedPayment) {
            $user->setPromisedPayment($promisedPayment);
        }
        if(($payments  = $this->paymentRepository->findByAccount($user->getAccount())) instanceof PaymentCollection) {
            $user->setPayments($payments);
        }
        if(!is_null($passport = $this->passportRepository->findById($user->getId()))) {
            $user->setPassportO($passport);
        }
        return $user;
    }
}
