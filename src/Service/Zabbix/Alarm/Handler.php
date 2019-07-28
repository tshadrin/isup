<?php
declare(strict_types = 1);


namespace App\Service\Zabbix\Alarm;


use App\Entity\UTM5\UTM5User;
use App\Entity\Zabbix\Alarm;
use App\Service\UTM5\UTM5DbService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class Handler
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var UTM5DbService
     */
    private $UTM5DbService;

    /**
     * ZabbixParser constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router, UTM5DbService $UTM5DbService)
    {
        $this->router = $router;
        $this->UTM5DbService = $UTM5DbService;
    }

    /**
     * Обработка данных из контроллера
     * @param string $subject
     * @param string $text
     * @return Alarm
     */
    public function handle(Command $command): Alarm
    {
        $text = $command->getMessage();
        $subject = $command->getSubject();
        $letter = null;
        if (false !== mb_strpos($text, "$$")) {
            [$text, $letter] = explode('$$', $text);
        }
        $ids = $this->getIdsFromText($text);
        $text = $this->replaceIdsToLinks($ids, $text);
        $text = html_entity_decode($text);
        $text = trim($text);
        $emails = $this->getEmailsFromIds($ids);
        $message = new Alarm($subject, $text, $ids, $emails, $letter);
        return $message;
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
            $url = $this->router->generate('search', ['type' => 'id', 'value' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
            $link = "[url={$url}]ID пользователя: {$id}[/url]";
            $message = preg_replace('/' . $id . '/', $link, $message);
        }

        return $message;
    }

    /**
     * Получение id клиентов из текста
     * @param string $text
     * @return array
     */
    private function getIdsFromText(string $text): array
    {
        $matches = $ids = [];
        preg_match_all('/\$[a-zA-z0-9]+\$/u', $text, $matches);
        if (count($matches) > 0) {
            foreach ($matches[0] as $match) {
                $ids[] = (int)trim($match, '$');
            }
        }

        return $ids;
    }

    /**
     * Получение email адресов по id
     * @param array $ids
     * @return array
     */
    private function getEmailsFromIds(array $ids): array
    {
        $emails = [];
        foreach ($ids as $id) {
            $user = $this->UTM5DbService->search((string)$id, 'id');
            if ($user instanceof UTM5User && (!is_null($email = $user->getEmail()))) {
                $emails[] = $user->getEmail();
            }
        }

        return $emails;
    }
}
