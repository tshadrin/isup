<?php
declare(strict_types=1);

namespace App\Service\Bitrix\User\Add;

use App\Service\Bitrix\BitrixRestService;
use App\Service\UTM5\URFAService;
use Psr\Log\LoggerInterface;

class Handler
{
    /** @var BitrixRestService  */
    private $bitrixRestService;
    /** @var URFAService  */
    private $URFAService;
    /** @var LoggerInterface  */
    private $logger;

    public function __construct(BitrixRestService $bitrixRestService, URFAService $URFAService, LoggerInterface $bitrixLogger)
    {
        $this->bitrixRestService = $bitrixRestService;
        $this->URFAService = $URFAService;
        $this->logger = $bitrixLogger;
    }

    public function handle(Command $command)
    {
        $login = $command->document[2]; //DEAL_<NUM>
        [,$dealId] = explode('_', $command->document[2]); //DEAL_<NUM>
        $deal = $this->bitrixRestService->getDeal($dealId);
        $uid = $this->URFAService->addUser($login, $deal->phone, $deal->address, $deal->name);
        $this->bitrixRestService->updateDeal($dealId, [BitrixRestService::DEAL_UTM5_ID_FIELD => $uid,]);
        $this->logger->info('User created', [
            'uid' => $uid, 'deal' => $deal->id,
            'phone' => $deal->phone, 'address' => $deal->address,
            'name' => $deal->name
        ]);
    }
}