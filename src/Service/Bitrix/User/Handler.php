<?php
declare(strict_types = 1);


namespace App\Service\Bitrix\User;


use App\Entity\UTM5\UTM5User;
use App\Service\UTM5\UTM5DbService;

class Handler
{
    /**
     * @var UTM5DbService
     */
    private $UTM5DbService;

    /**
     * @var UTM5User
     */
    private $user;

    public function __construct(UTM5DbService $UTM5DbService)
    {
        $this->UTM5DbService = $UTM5DbService;
    }

    public function handle(Command $command): array
    {
        $this->user = $this->UTM5DbService->search(self::cropPhonePrefix($command->phone), UTM5DbService::SEARCH_TYPE_PHONE);
        return $this->prepare();
    }

    private static function cropPhonePrefix(string $phone): string
    {
        return mb_substr($phone, 1);
    }

    private function prepare(): array
    {
        $user = $this->user;

        $userData = [
            'id' => $user->getId(),
            'full_name' => $user->getFullName(),
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
            'internet_status' => $user->isInternetStatus(),
            'address' => $user->getAddress(),

            'balance' => $user->getBalance(),
            'requirement_payment' => $user->getRequirementPayment(),
            'credit' => $user->getCredit(),
            'block' => $user->getBlock(),
            'promised_payment' => $user->isPromisedPayment(),
            'lifestrem_login' => $user->getLifestreamLogin(),
        ];
        $ips = [];
        foreach ($user->getIps() as $ip)
            $ips[] = $ip;
        $userData['ips'] = $ips;
        $tariffs = [];
        foreach ($user->getTariffs() as $tariff)
            $tariffs[] = ['name' => $tariff->getName(),'next_name' => $tariff->getNextName()];
        $userData['tarifs'] = $tariffs;
        return $userData;
    }


}