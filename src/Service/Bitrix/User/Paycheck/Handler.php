<?php
declare(strict_types = 1);


namespace App\Service\Bitrix\User\Paycheck;

use App\Service\UTM5\{BitrixRestService, UTM5DbService };

class Handler
{
    /**
     * @var BitrixRestService
     */
    private $bitrixRestService;
    /**
     * @var UTM5DbService
     */
    private $UTM5DbService;

    public function __construct(BitrixRestService $bitrixRestService, UTM5DbService $UTM5DbService)
    {
        $this->bitrixRestService = $bitrixRestService;
        $this->UTM5DbService = $UTM5DbService;
    }

    public function handle(Command $command): bool
    {
        [, $dealId] = explode('_', $command->document[2]); //DEAL_<NUM> exploding
        $dealData = $this->bitrixRestService->getDealDataById((int)$dealId);
        $user = $this->UTM5DbService->search((string)$dealData->utm5Id, UTM5DbService::SEARCH_TYPE_ID);
        if ($user->hasPaidForServices()) {
            $this->bitrixRestService->setDealWon($dealData);
            return true;
        }
        return false;
    }
}
