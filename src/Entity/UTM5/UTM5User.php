<?php
namespace App\Entity\UTM5;

/**
 * Class UTM5User
 * @package App\Entity\UTM5
 * Класс пользователя UTM5
 */
class UTM5User
{
    /**
     * @var int ID
     */
    protected $id;
    /**
     * @var string логин
     */
    protected $login;
    /**
     * @var string пароль
     */
    protected $password;
    /**
     * @var int аккаунт
     */
    protected $account;
    /**
     * @var string Ф.И.О.
     */
    protected $full_name;
    /**
     * @var array актуальный и юр. адрес
     */
    protected $addresses = [];
    /**
     * @var double баланс
     */
    protected $balance;
    /**
     * @var \Datetime дата подключения
     */
    protected $create_date;
    /**
     * @var int статус интернет
     */
    protected $int_status;
    /**
     * @var double сумма кредита
     */
    protected $credit;
    /**
     * @var array домашний, рабочий и мобильный телефоны
     */
    protected $phones = [];
    /**
     * @var array роутеры за которыми находится пользователь
     */
    protected $routers = [];
    /**
     * @var array список ip адресов пользователя
     */
    protected $ips = [];
    /**
     * @var array список групп в которые входит пользователь
     */
    protected $groups = [];
    /**
     * @var array услуги пользователя
     */
    protected $services = [];
    /**
     * @var array
     */
    protected $tariff = [];
    /**
     * @var string тип блокировки
     */
    protected $block;

    /**
     * @var UTM5UserComment комментарии
     */
    protected $comments;

    /**
     * @var array платежи
     */
    protected $payments;


    protected $email;

    /**
     * @var string цепочка свичей
     */
    protected $chain;


    protected $remind_me;

    /**
     * @var string паспортные данные
     */
    protected $passport;

    protected $lifestream_login;

    /**
     * Комментарии в утм
     * @var string
     */
    private $utm_comments;

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
    public function getLifestreamLogin()
    {
        return $this->lifestream_login;
    }

    /**
     * @return mixed
     */
    public function getId() { return $this->id; }
    /**
     * @return mixed
     */
    public function getFullName() { return $this->full_name; }
    /**
     * @return mixed
     */
    public function getLogin() { return $this->login; }
    /**
     * @return mixed
     */
    public function getAccount() { return $this->account; }
    /**
     * @return array
     */
    public function getAddresses() { return $this->addresses; }
    /**
     * @return array
     */
    public function getPhones() { return $this->phones; }
    /**
     * @return array
     */
    public function getServices() { return $this->services; }
    /**
     * @return array
     */
    public function getIps() { return $this->ips; }
    /**
     * @return array
     */
    public function getTariff() { return $this->tariff; }
    /**
     * @return array
     * Группы пользователя
     */
    public function getGroups() { return $this->groups; }
    /**
     * @return array
     */
    public function getRouters() { return $this->routers; }
    /**
     * @return float
     */
    public function getBalance() { return $this->balance; }
    /**
     * @return float
     */
    public function getCredit() { return $this->credit; }
    /**
     * @return int
     */
    public function getIntStatus() { return $this->int_status; }
    /**
     * @return string
     */
    public function getPassword() { return $this->password; }
    /**
     * @return string
     */
    public function getBlock() { return $this->block; }
    /**
     * @return array
     */
    public function getPayments() { return $this->payments; }

    /**
     * @return string
     */
    public function getPassport(): string
    {
        return $this->passport;
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
    public function getRemindMe()
    {
        return $this->remind_me;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @param string $full_name
     */
    public function setFullName($full_name)
    {
        $this->full_name = $full_name;
    }

    /**
     * @return \Datetime
     */
    public function getCreateDate(): \Datetime
    {
        return $this->create_date;
    }

    /**
     * @param \Datetime $create_date
     */
    public function setCreateDate(string $create_date): void
    {
        $this->create_date = \DateTime::createFromFormat("U", $create_date);
    }

    /**
     * @param array $addresses
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * @param array $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     * @param array $ips
     */
    public function setIps($ips)
    {
        $this->ips = $ips;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @param array $phones
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
    }

    /**
     * @param array $routers
     */
    public function setRouters($routers)
    {
        $this->routers = $routers;
    }

    /**
     * @param array $services
     */
    public function setServices($services)
    {
        $this->services = $services;
    }

    /**
     * @param array $tariff
     */
    public function setTariff($tariff)
    {
        $this->tariff = $tariff;
        $this->tariff['discount_period_start'] = \Datetime::createFromFormat("U", $this->tariff['discount_period_start']);
        $this->tariff['discount_period_end'] = \Datetime::createFromFormat("U", $this->tariff['discount_period_end']);
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param float $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @param float $credit
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
    }

    /**
     * @param int $int_status
     */
    public function setIntStatus($int_status)
    {
        $this->int_status = $int_status;
    }

    /**
     * @param string $block
     */
    public function setBlock($block)
    {
        $this->block = $block;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @param array $payments
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
    }

    /**
     * @param string $chain
     */
    public function setChain(string $chain): void
    {
        $this->chain = $chain;
    }

    /**
     * @param mixed $lifestream_login
     */
    public function setLifestreamLogin($lifestream_login): void
    {
        $this->lifestream_login = $lifestream_login;
    }

    /**
     * @param mixed $remind_me
     */
    public function setRemindMe($remind_me): void
    {
        $this->remind_me = $remind_me;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __get($name)
    {
        if(isset($this->$name))
            return $this->$name;
        return false;
    }

    public function addPhone($phone, $phone_type)
    {
        $this->phones[$phone_type] = $phone;
    }
    public function addAddress($address, $address_type)
    {
        $this->addresses[$address_type] = $address;
    }
    /**
     * @param $name
     * @param $value
     * @return bool
     */
    public function __set($name, $value)
    {
        if(isset($this->$name))
            $this->$name = $value;
        return false;
    }
    public function getAddress($type)
    {
        if(array_key_exists($type, $this->addresses))
            return $this->addresses[$type];
    }
    public function getPhone($type)
    {
        if(array_key_exists($type, $this->phones))
            return $this->phones[$type];
    }

    /**
     * @return string
     */
    public function getUtmComments(): string
    {
        return $this->utm_comments;
    }

    /**
     * @param string $utm_comments
     */
    public function setUtmComments(string $utm_comments): void
    {
        $this->utm_comments = $utm_comments;
    }

    /**
     * @param string $passport
     */
    public function setPassport(string $passport): void
    {
        $this->passport = $passport;
    }

    public static function factory(array $data)
    {
        $user = new self;
        $user->setId($data['id']);
        $user->setLogin($data['login']);
        $user->setPassword($data['password']);
        $user->setAccount($data['basic_account']);
        $user->setFullName($data['full_name']);
        $user->setBalance($data['balance']);
        $user->setCredit($data['credit']);
        $user->setIntStatus($data['int_status']);
        $user->setEmail($data['email']);
        $user->setCreateDate($data['create_date']);
        $user->setPassport($data['passport']);
        $user->setLifestreamLogin($data['lifestream_login']);
        $user->setRemindMe($data['remind_me']);
        $user->addPhone($data['home_telephone'], 'home');
        $user->addPhone($data['mobile_telephone'], 'mobile');
        $user->addPhone($data['work_telephone'], 'work');
        $user->addAddress($data['juridical_address'], 'juridical');
        if (strlen($data['flat_number'])) {
            $data['actual_address'] .= ' - '.$data['flat_number'];
        }
        $user->addAddress($data['actual_address'], 'actual');
        $user->setIps($data['ips']);
        $user->setGroups($data['groups']);
        $user->setRouters($data['routers']);
        $user->setServices($data['services']);
        if(!is_null($data['tariff']))
            $user->setTariff($data['tariff']);
        $user->setBlock($data['block']);
        $user->setComments($data['comments']);
        $user->setPayments($data['payments']);
        $user->setUtmComments($data['utm_comments']);
        return $user;
    }

    public static function factoryPartial(array $data)
    {
        $user = new self;
        $user->setId($data['id']);
        $user->setLogin($data['login']);
        $user->setPassword($data['password']);
        $user->setAccount($data['basic_account']);
        $user->setFullName($data['full_name']);
        $user->setBalance($data['balance']);
        $user->setCredit($data['credit']);
        $user->setEmail($data['email']);
        $user->setIntStatus($data['int_status']);
        $user->addPhone($data['home_telephone'], 'home');
        $user->addPhone($data['mobile_telephone'], 'mobile');
        $user->addPhone($data['work_telephone'], 'work');
        $user->addAddress($data['juridical_address'], 'juridical');
        $user->setUtmComments($data['utm_comments']);
        if (strlen($data['flat_number'])) {
            $data['actual_address'] .= ' - '.$data['flat_number'];
        }
        $user->addAddress($data['actual_address'], 'actual');
        return $user;
    }

    /**
     * @param $requirement_payment
     */
    public function setRequirementPayment($requirement_payment)
    {
        $this->requirement_payment = $requirement_payment;
    }

    /**
     * @return mixed
     */
    public function getRequirementPayment()
    {
        return $this->requirement_payment;
    }
}
