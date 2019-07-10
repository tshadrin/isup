<?php
declare(strict_types = 1);


namespace App\Service\Zabbix;


use App\Entity\UTM5\UTM5User;
use App\Entity\Zabbix\Message;
use App\Service\UTM5\UTM5DbService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ZabbixService
 * @package App\Service\Zabbix
 */
class ZabbixService
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
     * @param string $subject
     * @param string $text
     * @return Message
     */
    public function handle(string $subject, string $text): Message
    {
        $letter = null;
        if (false !== mb_strpos($text, "$$")) {
            [$text, $letter] = explode('$$', $text);
        }
        $ids = $this->getIdsFromText($text);
        $text = $this->replaceIdsToLinks($ids, $text);
        $text = html_entity_decode($text);
        $text = trim($text);
        $emails = $this->getEmailsFromIds($ids);
        $message = new Message($subject, $text, $ids, $emails, $letter);

        return $message;
    }

    /**
     * @param array $ids
     * @param string $message
     * @return string
     */
    private function replaceIdsToLinks(array $ids, string $message): string
    {
        foreach ($ids as $id) {
            $url = $this->router->generate('search', ['type' => 'id', 'value' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
            $link = "[url={$url}]ID пользователя: {$id}[/url]";
            $message = preg_replace('/'.$id.'/', $link, $message);
        }

        return $message;
    }

    /**
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
     * @param array $ids
     * @return array
     */
    private function getEmailsFromIds(array $ids): array
    {
        $emails = [];
        foreach ($ids as $id) {
            $user = $this->UTM5DbService->search((string)$id, 'id');
            if ($user instanceof UTM5User && (!is_null($email = $user->getEmail()))){
                $emails[] = $user->getEmail();
            }
        }
        return $emails;
    }
}
