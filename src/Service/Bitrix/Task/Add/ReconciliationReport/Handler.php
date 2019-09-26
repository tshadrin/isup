<?php
declare(strict_types=1);

namespace App\Service\Bitrix\Task\Add\ReconciliationReport;


use App\Service\Bitrix\BitrixRestService;
use App\Service\UTM5\UTM5DbService;

class Handler
{
    const RESPONSIBLE_ID = 38;
    const ACCOMPLICES = 490;
    const GROUP_ID = 46;
    const PRIORITY = 2;
    const AUDITORS = 1;

    /** @var UTM5DbService  */
    private $UTM5DbService;
    /** @var BitrixRestService  */
    private $bitrixRestService;

    public function __construct(UTM5DbService $UTM5DbService, BitrixRestService $bitrixRestService)
    {
        $this->UTM5DbService = $UTM5DbService;
        $this->bitrixRestService = $bitrixRestService;
    }

    public function handle(Command $command): void
    {
        $date = $command->period;
        $utm5Id = $command->utm5Id;
        $utm5User = $this->UTM5DbService->search((string)$utm5Id, UTM5DbService::SEARCH_TYPE_ID);
        $orgName = $utm5User->getFullName();
        $phone = $utm5User->getMobilePhone();
        $email = $utm5User->getEmail();
        $data = [
            'TITLE' => "[Mon] Юридическое лицо {$orgName} (id {$utm5Id} - запрос акта сверки",
            'DESCRIPTION' => "Юридическое лицо {$orgName}  (id {$utm5Id}) запрашивает акт сверки за {$date->format("m - Y")} года. Телефон: +7{$phone}, E-mail: {$email}.",
            'RESPONSIBLE_ID' => self::RESPONSIBLE_ID, // исполнитель
            'ACCOMPLICES' => self::ACCOMPLICES, // "" соисполнитель
            'GROUP_ID' => self::GROUP_ID, // группа
            'PRIORITY' => self::PRIORITY, // приоритет
            'AUDITORS' => self::AUDITORS, // "" наблюдатель
        ];
        $task = $this->bitrixRestService->addTask($data);
    }
}