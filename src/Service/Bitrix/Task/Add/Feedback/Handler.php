<?php
declare(strict_types=1);

namespace App\Service\Bitrix\Task\Add\Feedback;

use App\Service\Bitrix\BitrixRestService;
use App\Service\UTM5\UTM5DbService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Handler
{
    const RESPONSIBLE_ID = 38;
    const ACCOMPLICES = 490;
    const GROUP_ID = 46;
    const PRIORITY = 2;
    const AUDITORS = 1;

    /** @var BitrixRestService  */
    private $bitrixRestService;
    /** @var object|string  */
    private $user;
    /** @var UTM5DbService  */
    private $UTM5DbService;

    public function __construct(BitrixRestService $bitrixRestService, TokenStorageInterface $tokenStorage, UTM5DbService $UTM5DbService)
    {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->bitrixRestService = $bitrixRestService;
        $this->UTM5DbService = $UTM5DbService;
    }

    public function handle(Command $command): void
    {
        $utm5Id= $command->utm5Id;
        $utm5User = $this->UTM5DbService->search((string)$utm5Id, UTM5DbService::SEARCH_TYPE_ID);
        $orgName = $utm5User->getFullName();
        $phone = $utm5User->getMobilePhone();
        $email = $utm5User->getEmail();
        $comment = $command->comment;
        $user = $this->user;
        $data = [
            'TITLE' => "[Mon] Юридическое лицо {$orgName} (id {$utm5Id} - запрос обратной связи",
            'DESCRIPTION' => "Юридическое лицо {$orgName}  (id {$utm5Id}) запрашивает консультацию менеджера. Телефон: +7{$phone}, E-mail: {$email}.\n",
            'RESPONSIBLE_ID' => self::RESPONSIBLE_ID, // исполнитель
            'ACCOMPLICES' => self::ACCOMPLICES, // "" соисполнитель
            'GROUP_ID' => self::GROUP_ID, // группа
            'PRIORITY' => self::PRIORITY, // приоритет
            'AUDITORS' => self::AUDITORS, // "" наблюдатель
        ];
        $data["DESCRIPTION"] .= "Комментарий оператора: {$comment}.\n Запрос сформировал {$user->getFullName()}";
        $task = $this->bitrixRestService->addTask($data);
    }
}