<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use App\Collection\UTM5\{ GroupCollection, PaymentCollection, RouterCollection, ServiceCollection, TariffCollection };

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

    /** @var int */
    protected $id;
    /** @var string */
    protected $login;
    /** @var string */
    protected $email;
    /** @var string */
    protected $password;
    /** @var int */
    protected $account;
    /** @var string */
    protected $fullName;
    /** @var string */
    protected $passport;
    /** @var Passport */
    protected $passportO;
    /** @var \DateTimeImmutable */
    protected $created;
    /** @var array */
    protected $ips;
    /** @var array */
    protected $ips6;
    /** @var GroupCollection */
    protected $groups;
    /** @var House */
    protected $house;
    /** @var string */
    protected $flatNumber;
    /** @var string */
    protected $address;
    /** @var string */
    protected $juridicalAddress;
    /** @var float */
    protected $balance;
    /** @var array */
    protected $phones = [];
    /** @var string */
    protected $UTM5Comments;
    /** @var float */
    protected $credit;
    /** @var RouterCollection */
    protected $routers;
    /** @var ServiceCollection */
    protected $services;
    /** @var TariffCollection */
    protected $tariffs;
    /** @var string */
    protected $managerNotice;
    /** @var string */
    protected $lifestreamLogin;
    /** @var string */
    protected $lifestreamId;
    /** @var int */
    protected $block;
    /** @var string */
    protected $additionalPhone;
    /** @var PromisedPayment */
    protected $promisedPayment;
    /** @var PaymentCollection */
    protected $payments;
    /** @var bool */
    protected $internetStatus;
    /** @var bool */
    protected $remindMe;
    /** @var bool */
    protected $juridical;

    public function getId(): int
    {
        return $this->id;
    }
    public function getLogin(): string
    {
        return $this->login;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function getAccount(): int
    {
        return $this->account;
    }
    public function getFullName(): string
    {
        return $this->fullName;
    }
    public function getPassport(): string
    {
        return $this->passport;
    }
    public function getPassportO(): ?Passport
    {
        return $this->passportO;
    }
    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }
    public function getIps(): ?array
    {
        return $this->ips;
    }
    public function getIps6(): ?array
    {
        return $this->ips6;
    }
    public function getGroups(): GroupCollection
    {
        return $this->groups;
    }
    public function getHouse(): ?House
    {
        return $this->house;
    }
    public function getFlatNumber(): string
    {
        return $this->flatNumber;
    }
    public function getAddress(): ?string
    {
        if(!empty($flatNumber = $this->getFlatNumber())) {
            return "{$this->address} - {$this->getFlatNumber()}";
        } else {
            return $this->address;
        }
    }
    public function getJuridicalAddress(): ?string
    {
        return $this->juridicalAddress;
    }
    public function getBalance(): float
    {
        return $this->balance;
    }
    public function getPhones(): ?array
    {
        return $this->phones;
    }
    public function getUTM5Comments(): ?string
    {
        return $this->UTM5Comments;
    }
    public function getCredit(): float
    {
        return $this->credit;
    }
    public function getRouters(): ?RouterCollection
    {
        return $this->routers;
    }
    public function getServices(): ?ServiceCollection
    {
        return $this->services;
    }
    public function getTariffs(): ?TariffCollection
    {
        return $this->tariffs;
    }
    public function getManagerNotice(): ?string
    {
        return $this->managerNotice;
    }
    public function getLifestreamLogin(): ?string
    {
        return $this->lifestreamLogin;
    }
    public function getLifestreamId(): ?string
    {
        return $this->lifestreamId;
    }
    public function getBlock(): string
    {
        if($this->block === self::ADMIN_BLOCK)
            return "Admin block";
        if($this->block === self::SYSTEM_BLOCK)
            return "System block";
        return "No block";
    }
    public function getAdditionalPhone(): ?string
    {
        return $this->additionalPhone;
    }
    public function getPromisedPayment(): ?PromisedPayment
    {
        return $this->promisedPayment;
    }
    public function getPayments(): ?PaymentCollection
    {
        return $this->payments;
    }
    /** custom getters */
    public function getActualAddress(): ?string
    {
        if(($house = $this->getHouse()) instanceof House) {
            if(!empty($flatNumber = $this->getFlatNumber())) {
                return "{$house->__toString()} - {$flatNumber}";
            } else {
                return $house->__toString();
            }
        }
        return null;
    }
    public function getMobilePhone(): ?MobilePhone
    {
        if(array_key_exists('mobile', $phones = $this->getPhones())) {
            return $phones['mobile'];
        }
        return null;
    }
    public function getHomePhone(): ?string
    {
        if(array_key_exists('home', $phones = $this->getPhones())) {
            return $phones['home'];
        }
        return null;
    }
    public function getWorkPhone(): ?string
    {
        if(array_key_exists('work', $phones = $this->getPhones())) {
            return $phones['work'];
        }
        return null;
    }

    public function isInternetStatus(): bool
    {
        return $this->internetStatus;
    }
    public function isRemindMe(): bool
    {
        return $this->remindMe;
    }
    public function isJuridical(): bool
    {
        return $this->juridical;
    }
    public function isAdditionalPhone(): bool {
        return !is_null($this->additionalPhone);
    }
    public function hasIps(): bool
    {
        return !is_null($this->getIps());
    }
    public function hasIps6(): bool
    {
        return !is_null($this->ips6);
    }
    public function hasPaidForServices(): bool
    {
        if (!is_null($this->payments)) {
            return $this->getTotalPaymentsAmount() > 0.0;
        } else {
            return false;
        }
    }
    private function getTotalPaymentsAmount(): float
    {
        $this->payments->forAll(function ($num, $payment) use (&$amount){
            if($payment->getAmount() > 0)
                $amount += $payment->getAmount();
            return true;
        });
        return $amount;
    }

    /** custom checkers */
    public function isPromisedPayment(): bool
    {
        return is_object($this->promisedPayment);
    }
    public function isBlock(): bool
    {
        return (bool)$this->block;
    }


    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    public function setAccount(int $account): void
    {
        $this->account = $account;
    }
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }
    public function setPassport(string $passport): void
    {
        $this->passport = $passport;
    }
    public function setPassportO(Passport $passportO): void
    {
        $this->passportO = $passportO;
    }
    public function setCreated(\DateTimeImmutable $created): void
    {
        $this->created = $created;
    }
    public function setIps(array $ips): void
    {
        $this->ips = $ips;
    }
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
    public function setGroups(GroupCollection $groups): void
    {
        $this->groups = $groups;
    }
    public function setHouse(House $house): void
    {
        $this->house = $house;
    }
    public function setFlatNumber(string $flatNumber): void
    {
        $this->flatNumber = $flatNumber;
    }
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }
    public function setJuridicalAddress(string $juridicalAddress): void
    {
        $this->juridicalAddress = $juridicalAddress;
    }
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }
    public function setPhones(array $phones): void
    {
        $this->phones = $phones;
    }
    public function setUTM5Comments(string $UTM5Comments): void
    {
        $this->UTM5Comments = $UTM5Comments;
    }
    public function setCredit(float $credit): void
    {
        $this->credit = $credit;
    }
    public function setRouters(RouterCollection $routers): void
    {
        $this->routers = $routers;
    }
    public function setServices(ServiceCollection $services): void
    {
        $this->services = $services;
    }
    public function setTariffs(TariffCollection $tariffs): void
    {
        $this->tariffs = $tariffs;
    }
    public function setManagerNotice(string $managerNotice): void
    {
        $this->managerNotice = $managerNotice;
    }
    public function setLifestreamLogin(string $lifestreamLogin): void
    {
        $this->lifestreamLogin = $lifestreamLogin;
    }
    public function setLifestreamId(string $lifestreamId): void
    {
        $this->lifestreamId = $lifestreamId;
    }
    public function setBlock(int $block): void
    {
        $this->block = $block;
    }
    public function setPromisedPayment(PromisedPayment $promisedPayment): void
    {
        $this->promisedPayment = $promisedPayment;
    }
    public function setPayments(PaymentCollection $payments): void
    {
        $this->payments = $payments;
    }
    public function setInternetStatus(bool $internetStatus): void
    {
        $this->internetStatus = $internetStatus;
    }
    public function setRemindMe($remindMe): void
    {
        $this->remindMe = $remindMe;
    }
    public function setAdditionalPhone(?string $additionalPhone): void
    {
        $this->additionalPhone = $additionalPhone;
    }
    public function setJuridical(bool $juridical): void
    {
        $this->juridical = $juridical;
    }
    /** custom setters */
    public function setMobilePhone(MobilePhone $phone): void
    {
        $this->phones['mobile'] = $phone;
    }
    public function setHomePhone(string  $phone): void
    {
        $this->phones['home'] = $phone;
    }
    public function setWorkPhone(string $phone): void
    {
        $this->phones['work'] = $phone;
    }

    protected $chain;
    protected $comments;
    protected $requirement_payment;
    public function getChain(): ?string
    {
        return $this->chain;
    }
    public function getComments()
    {
        return $this->comments;
    }
    public function getRequirementPayment()
    {
        return $this->requirement_payment;
    }
    public function setChain(string $chain): void
    {
        $this->chain = $chain;
    }
    public function setComments($comments)
    {
        $this->comments = $comments;
    }
    public function setRequirementPayment($requirement_payment)
    {
        $this->requirement_payment = $requirement_payment;
    }

    public function getDiscountDate(): string
    {
        if (count($this->tariffs->toArray()) > 0) {
            $tariff = $this->tariffs[array_key_first($this->tariffs->toArray())];
            $discountPeriod = $tariff->getDiscountPeriod();
            return $discountPeriod->getEnd()->format("d-m-Y");
        } else {
            throw new \DomainException("No active tariffs.");
        }
    }
}