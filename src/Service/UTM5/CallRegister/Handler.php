<?php
declare(strict_types=1);

namespace App\Service\UTM5\CallRegister;



use App\ReadModel\UTM5\UserFetcher;
use phpcent\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Handler
{
    /**
     * @var Client
     */
    private $centrifugo;
    /**
     * @var UserFetcher
     */
    private $userFetcher;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(Client $centrifugo, UserFetcher $userFetcher, UrlGeneratorInterface $urlGenerator)
    {
        $this->centrifugo = $centrifugo;
        $this->userFetcher = $userFetcher;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(Command $command): void
    {
        try {
            $userFields = $this->userFetcher->getUserByPhone(mb_substr($command->callerNumber, 2));
            $url = $this->urlGenerator->generate("search.by.data", ["value" => $userFields['id'], "type" => 'id']);
        } catch(\DomainException $e) {
            $this->centrifugo->setSafety(false);
            $this->centrifugo->publish('calls', ['message'=>$e->getMessage()]);
        }
        $this->centrifugo->setSafety(false);
        $this->centrifugo->publish('calls', ['message'=>"<div style=\"font-size:1rem;\">{$command->callerNumber}<p>{$userFields['full_name']}</p><a class=\" bg-success\" href=\"{$url}\">Отрыть карточку клиента</a></div>"]);
    }
}