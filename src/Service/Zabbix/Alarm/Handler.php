<?php
declare(strict_types = 1);

namespace App\Service\Zabbix\Alarm;

use App\Entity\UTM5\UTM5User;
use App\Entity\Zabbix\Alarm;
use App\Service\UTM5\UTM5DbService;
use App\Service\Zabbix\Notifier\ChatNotifier;
use App\Service\Zabbix\Notifier\EmailNotifier;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class Handler
{
    const MESSAGE_DELIMITER = '$$';
    const IDS_PATTERN = '/\$[a-zA-z0-9]+\$/u';

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var UTM5DbService
     */
    private $UTM5DbService;
    /**
     * @var EmailNotifier
     */
    private $emailNotifier;
    /**
     * @var ChatNotifier
     */
    private $chatNotifier;

    /**
     * ZabbixParser constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router, UTM5DbService $UTM5DbService,
                                EmailNotifier $emailNotifier, ChatNotifier $chatNotifier)
    {
        $this->router = $router;
        $this->UTM5DbService = $UTM5DbService;
        $this->emailNotifier = $emailNotifier;
        $this->chatNotifier = $chatNotifier;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void
    {
        if ($this->isMessageContainsLetter($command->message)) {
            [$text, $letter] = $this->separateMessage($command->message);
        } else {
            $text = $command->message;
        }

        $ids = $this->getIdsFromText($text);
        $userData = $this->getUsersDataFromIds($ids);

        $text = $this->prepareText($ids, $userData->fullNames,  $text);

        $alarm = new Alarm(
            $command->subject,
            $text,
            $ids,
            $userData->emails,
            $letter ?? null
        );

        $alarm->setNotifiers([$this->emailNotifier, $this->chatNotifier]);
        $alarm->notify();
    }

    /**
     * @param string $text
     * @return bool
     */
    private function isMessageContainsLetter(string $text): bool
    {
        return false !== mb_strpos($text, self::MESSAGE_DELIMITER);
    }

    /**
     * @param string $message
     * @return array
     */
    private function separateMessage(string $message): array
    {
        return array_map('trim', explode(self::MESSAGE_DELIMITER, $message));
    }

    /**
     * Получение id клиентов из текста
     * @param string $text
     * @return int[]
     */
    private function getIdsFromText(string $text): array
    {
        $matches = $ids = [];
        preg_match_all(self::IDS_PATTERN, $text, $matches);
        if (count($matches) > 0) {
            foreach ($matches[0] as $match) {
                $ids[] = (int)trim($match, '$');
            }
        }

        return $ids;
    }

    /**
     * Достает необходимые данные пользователя (fullnames, emails)
     * @param array $ids
     * @return UsersData
     */
    private function getUsersDataFromIds(array $ids): UsersData
    {
        $usersData = new UsersData();
        foreach ($ids as $id) {
            $user = $this->UTM5DbService->search((string)$id, 'id');
            if ($user instanceof UTM5User && (!is_null($email = $user->getEmail()))) {
                $usersData->emails[] = $user->getEmail();
                $usersData->fullNames[] = $user->getFullName();
            }
        }

        return $usersData;
    }

    /**
     * Подготавливает текст оповещения
     * @param string $text
     * @return string
     */
    private function prepareText(array $ids, array $fullNames, string $text): string
    {
        $text = $this->replaceIdsToLinks($ids, $text);
        $text = $this->addUsersFullNamesToText($fullNames, $text);
        $text = html_entity_decode(htmlspecialchars_decode($text));

        return $text;
    }

    /**
     * Замена id на ссылки в тексте
     * @param array $ids
     * @param string $message
     * @return string
     */
    private function replaceIdsToLinks(array $ids, string $message): string
    {
        foreach ($ids as $id) {
            $url = $this->router->generate(
                'search.by.data',
                ['type' => 'id', 'value' => $id],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $link = "([url={$url}]ID пользователя: {$id}[/url])";
            $message = preg_replace('/' . $id . '/', $link, $message);
        }

        return $message;
    }

    /**
     * Добавляет наименования организаций/имена пользователей в текст оповещения
     * @param array $fullNames
     * @param $text
     * @return string
     */
    private function addUsersFullNamesToText(array $fullNames, $text): string
    {
        $text .= PHP_EOL;
        foreach ($fullNames as $fullName) {
            $text .= $fullName . PHP_EOL;
        }

        return $text;
    }

}
