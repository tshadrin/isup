<?php
declare(strict_types = 1);


namespace App\Service\Bitrix\User\PayCheck;

use App\Service\Bitrix\Deal;
use App\Service\UTM5\UTM5DbService;
use App\Service\Bitrix\BitrixRestService;
use Psr\Log\LoggerInterface;

class Handler
{
    /** @var BitrixRestService  */
    private $bitrixRestService;
    /** @var UTM5DbService  */
    private $UTM5DbService;
    /** @var LoggerInterface  */
    private $logger;

    public function __construct(BitrixRestService $bitrixRestService, UTM5DbService $UTM5DbService, LoggerInterface $bitrixLogger)
    {
        $this->bitrixRestService = $bitrixRestService;
        $this->UTM5DbService = $UTM5DbService;
        $this->logger = $bitrixLogger;
    }

    public function handle(Command $command): void
    {
        [, $dealId] = explode('_', $command->document[2]); //DEAL_<NUM> exploding
        $deal = $this->bitrixRestService->getDeal((int)$dealId);
        $user = $this->UTM5DbService->search((string)$deal->utm5Id, UTM5DbService::SEARCH_TYPE_ID);
        if($this->isEmptyUTM5IdOrStatus($deal)) {
            $this->logger->info("Deal {$command->document[2]}not updated. Not all required field filled");
            throw new \DomainException("Deal {$command->document[2]}not updated. Not all required field filled");
        }
        if ($user->hasPaidForServices()) {
            $this->bitrixRestService->setDealWon($deal);
            $this->logger->info("Deal {$command->document[2]} updated");
        } else {
            $this->logger->info("Deal {$command->document[2]} not updated");
        }
    }

    private function isEmptyUTM5IdOrStatus(Deal $deal): bool
    {
        return empty($deal->utm5Id) && empty($deal->status);
    }
}