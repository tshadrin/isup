<?php
namespace App\Entity\UTM5;

use App\Collection\UTM5\GroupCollection;
use App\Collection\UTM5\PaymentCollection;
use App\Collection\UTM5\RouterCollection;
use App\Collection\UTM5\ServiceCollection;
use App\Collection\UTM5\TariffCollection;

/**
 * Class UTM5User
 * @package App\Entity\UTM5
 * Класс пользователя UTM5
 */
class UTM5User
{
    const ADMIN_BLOCK = 2;
    const SYSTEM_BLOCK = 1;
    const NO_BLOCK = 0;

    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $login;
    /**
     * @var string
     */
    protected $email;
    /**
     * @var string
     */
    protected $password;
    /**
     * @var int
     */
    protected $account;
    /**
     * @var string
     */
    protected $fullName;
    /**
     * @var string
     */
    protected $passport;
    /**
     * @var \DateTimeImmutable
     */
    protected $created;
    /**
     * @var array
     */
    protected $ips;

    /**
     * @var array
     */
    protected $ips6;

    /**
     * @var GroupCollection
     */
    protected $groups;
    /**
     * @var House
     */
    protected $house;
    /**
     * @var string
     */
    protected $flatNumber;
    /**
     * @var string
     */
    protected $address;
    /**
     * @var string
     */
    protected $juridicalAddress;
    /**
     * @var float
     */
    protected $balance;
    /**
     * @var bool
     */
    protected $internetStatus;
    /**
     * @var array
     */
    protected $phones = [];
    /**
     * @var string
     */
    protected $UTM5Comments;
    /**
     * @var float
     */
    protected $credit;
    /**
     * @var RouterCollection
     */
    protected $routers;
    /**
     * @var ServiceCollection
     */
    protected $services;
    /**
     * @var TariffCollection
     */
    protected $tariffs;
    /**
     * @var bool
     */
    protected $remindMe;
    /**
     * @var string
     */
    protected $lifestreamLogin;
    /**
     * @var int
     */
    protected $block;
    /**
     * @var PromisedPayment
     */
    protected $promisedPayment;

    /**
     * @var PaymentCollection
     */
    protected $payments;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getAccount(): int
    {
        return $this->account;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getPassport(): string
    {
        return $this->passport;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * @return array
     */
    public function getIps(): ?array
    {
        return $this->ips;
    }

    /**
     * @return array
     */
    public function getIps6(): ?array
    {
        return $this->ips6;
    }

    /**
     * @return GroupCollection
     */
    public function getGroups(): GroupCollection
    {
        return $this->groups;
    }

    /**
     * @return House
     */
    public function getHouse(): ?House
    {
        return $this->house;
    }

    /**
     * @return string
     */
    public function getFlatNumber(): string
    {
        return $this->flatNumber;
    }

    /**
     * Возвращает адрес по дому или, если нет дома, то из поля address
     * @return string
     */
    public function getActualAddress(): ?string
    {
        if(($house = $this->getHouse()) instanceof House) {
            if(!empty($flatNumber = $this->getFlatNumber())) {
                return "{$house->__toString()} - {$flatNumber}";
            } else {
                return $house->__toString();
            }
        }
    }

    /**
     * @return string
     */
    public function getAddress(): ?string
    {
        if(!empty($flatNumber = $this->getFlatNumber())) {
            return "{$this->address} - {$this->getFlatNumber()}";
        } else {
            return $this->address;
        }
    }

    /**
     * @return string
     */
    public function getJuridicalAddress(): ?string
    {
        return $this->juridicalAddress;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @return array
     */
    public function getPhones(): ?array
    {
        return $this->phones;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        if(array_key_exists('mobile', $phones = $this->getPhones())) {
            return $phones['mobile'];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getHomePhone(): ?string
    {
        if(array_key_exists('home', $phones = $this->getPhones())) {
            return $phones['home'];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getWorkPhone(): ?string
    {
        if(array_key_exists('work', $phones = $this->getPhones())) {
            return $phones['work'];
        }
        return null;
    }

    /**
     * @return string
     */
    public function getUTM5Comments(): ?string
    {
        return $this->UTM5Comments;
    }

    /**
     * @return float
     */
    public function getCredit(): float
    {
        return $this->credit;
    }

    /**
     * @return RouterCollection
     */
    public function getRouters(): ?RouterCollection
    {
        return $this->routers;
    }

    /**
     * @return ServiceCollection
     */
    public function getServices(): ?ServiceCollection
    {
        return $this->services;
    }

    /**
     * @return TariffCollection
     */
    public function getTariffs(): ?TariffCollection
    {
        return $this->tariffs;
    }

    /**
     * @return string
     */
    public function getLifestreamLogin(): ?string
    {
        return $this->lifestreamLogin;
    }

    /**
     * @return int
     */
    public function getBlock(): string
    {
        if($this->block === self::ADMIN_BLOCK)
            return "Admin block";
        if($this->block === self::SYSTEM_BLOCK)
            return "System block";
        return "No block";
    }

    /**
     * @return PromisedPayment
     */
    public function getPromisedPayment(): ?PromisedPayment
    {
        return $this->promisedPayment;
    }

    /**
     * @return PaymentCollection
     */
    public function getPayments(): ?PaymentCollection
    {
        return $this->payments;
    }

    /**
     * @return bool
     */
    public function isPromisedPayment(): bool
    {
        return is_object($this->promisedPayment);
    }

    /**
     * @return bool
     */
    public function isBlock(): bool
    {
        return (bool)$this->block;
    }

    /**
     * @return bool
     */
    public function isInternetStatus(): bool
    {
        return $this->internetStatus;
    }

    /**
     * @return bool
     */
    public function isRemindMe(): bool
    {
        return $this->remindMe;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param int $account
     */
    public function setAccount(int $account): void
    {
        $this->account = $account;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @param string $passport
     */
    public function setPassport(string $passport): void
    {
        $this->passport = $passport;
    }

    /**
     * @param \DateTimeImmutable $created
     */
    public function setCreated(\DateTimeImmutable $created): void
    {
        $this->created = $created;
    }

    /**
     * @param array $ips
     */
    public function setIps(array $ips): void
    {
        $this->ips = $ips;
    }

    /**
     * @param array $ips6
     */
    public function setIps6(array $ips6): void
    {
        foreach($ips6 as $ip6) {
            list($o1,$o2,$o3,$o4) = explode(':', $ip6);
            $o3 = ltrim($o3, '0');
            $o4 = ltrim($o4, '0');
            $this->ips6['wan'] = "{$o1}:{$o2}:{$o3}::{$o4}";
            $this->ips6['local_net'] = "{$o1}:{$o2}:{$o3}:{$o4}::/64";
        }
    }

    /**
     * @param GroupCollection $groups
     */
    public function setGroups(GroupCollection $groups): void
    {
        $this->groups = $groups;
    }

    /**
     * @param House $house
     */
    public function setHouse(House $house): void
    {
        $this->house = $house;
    }

    /**
     * @param string $flatNumber
     */
    public function setFlatNumber(string $flatNumber): void
    {
        $this->flatNumber = $flatNumber;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @param string $juridicalAddress
     */
    public function setJuridicalAddress(string $juridicalAddress): void
    {
        $this->juridicalAddress = $juridicalAddress;
    }

    /**
     * @param float $balance
     */
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    /**
     * @param bool $internetStatus
     */
    public function setInternetStatus(bool $internetStatus): void
    {
        $this->internetStatus = $internetStatus;
    }

    /**
     * @param array $phones
     */
    public function setPhones(array $phones): void
    {
        $this->phones = $phones;
    }

    /**
     * @param string $phone
     */
    public function setMobilePhone(string $phone): void
    {
        $this->phones['mobile'] = $phone;
    }

    /**
     * @param string $phone
     */
    public function setHomePhone(string  $phone): void
    {
        $this->phones['home'] = $phone;
    }

    /**
     * @param string $phone
     */
    public function setWorkPhone(string $phone): void
    {
        $this->phones['work'] = $phone;
    }

    /**
     * @param string $UTM5Comments
     */
    public function setUTM5Comments(string $UTM5Comments): void
    {
        $this->UTM5Comments = $UTM5Comments;
    }

    /**
     * @param float $credit
     */
    public function setCredit(float $credit): void
    {
        $this->credit = $credit;
    }

    /**
     * @param RouterCollection $routers
     */
    public function setRouters(RouterCollection $routers): void
    {
        $this->routers = $routers;
    }

    /**
     * @param ServiceCollection $services
     */
    public function setServices(ServiceCollection $services): void
    {
        $this->services = $services;
    }

    /**
     * @param TariffCollection $tariffs
     */
    public function setTariffs(TariffCollection $tariffs): void
    {
        $this->tariffs = $tariffs;
    }

    /**
     * @param $remindMe
     */
    public function setRemindMe($remindMe): void
    {
        $this->remindMe = $remindMe;
    }

    /**
     * @param string $lifestreamLogin
     */
    public function setLifestreamLogin(string $lifestreamLogin): void
    {
        $this->lifestreamLogin = $lifestreamLogin;
    }

    /**
     * @param int $block
     */
    public function setBlock(int $block): void
    {
        $this->block = $block;
    }

    /**
     * @param PromisedPayment $promised_payment
     */
    public function setPromisedPayment(PromisedPayment $promisedPayment): void
    {
        $this->promisedPayment = $promisedPayment;
    }

    /**
     * @param PaymentCollection $payments
     */
    public function setPayments(PaymentCollection $payments): void
    {
        $this->payments = $payments;
    }

    /**
     * @var UTM5UserComment комментарии
     */
    protected $comments;
    /**
     * @var string цепочка свичей
     */
    protected $chain;
    /**
     * @var
     */
    protected $requirement_payment;

    /**
     * @return string|null
     */
    public function getChain(): ?string
    {
        return $this->chain;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return mixed
     */
    public function getRequirementPayment()
    {
        return $this->requirement_payment;
    }

    /**
     * @param string $chain
     */
    public function setChain(string $chain): void
    {
        $this->chain = $chain;
    }

    /**
     * @param $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @param $requirement_payment
     */
    public function setRequirementPayment($requirement_payment)
    {
        $this->requirement_payment = $requirement_payment;
    }

    public function getUnserializedPassport()
    {
        //dump($this->passport);exit;
    }
}
