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
        $this->user = $this->UTM5DbService->search(self::cropPhonePrefix($command->phone), 'phone');
        return $this->prepare();
    }

    private static function cropPhonePrefix(string $phone): string
    {
        return mb_substr($phone, 1);
    }

    private function prepare(): array
    {
        $user = $this->user;
        return [
            'id' => $user->getId(),
            'full_name' => $user->getFullName(),
            'requirement_payment' => $user->getRequirementPayment(),
            'balance' => $user->getBalance(),
        ];
    }


}