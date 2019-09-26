<?php
declare(strict_types=1);

namespace App\Service\Bitrix\User\Remove;

use App\Service\Bitrix\BitrixRestService;
use App\Service\Bitrix\Deal;
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

    public function handle(Command $command): void
    {
        [,$dealId] = explode('_', $command->document[2]); //DEAL <NUM>
        $deal = $this->bitrixRestService->getDeal($dealId);
        $this->logger->info("Remove user with deal {$command->document[2]}");

        if($this->isEmptyUTM5Id($deal)) {
            $this->logger->error("User cannot be removed. UTM5 Id is empty in deal {$command->document[2]}");
            throw new \DomainException("User cannot be removed. UTM5 Id is empty in deal {$command->document[2]}");
        }

        $this->URFAService->removeUser($deal->utm5Id);
        $this->bitrixRestService->updateDeal($dealId, [BitrixRestService::DEAL_UTM5_ID_FIELD => 0,]);
        $this->logger->info("User {$deal->utm5Id} deleted");
    }

    private function isEmptyUTM5Id(Deal $deal): bool
    {
        return empty($deal['utm5_id']);
    }
}