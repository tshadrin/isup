<?php
declare(strict_types=1);

namespace App\Service\UTM5\CallRegister;



use App\ReadModel\UTM5\UserFetcher;
use App\Repository\UserRepository;
use phpcent\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Client $centrifugo,
                                UserFetcher $userFetcher,
                                UrlGeneratorInterface $urlGenerator,
                                TokenStorageInterface $tokenStorage,
                                UserRepository $userRepository)
    {
        $this->centrifugo = $centrifugo;
        $this->userFetcher = $userFetcher;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
    }

    public function handle(Command $command): void
    {
        $this->centrifugo->setSafety(false);
        try {
            $user = $this->userRepository->findByInternalNumber($command->operatorNumber);
            if(!is_null($user)) {
                $userFields = $this->userFetcher->getUserByPhone(mb_substr($command->callerNumber, 2));
                $url = $this->urlGenerator->generate("search.by.data", ["value" => $userFields['id'], "type" => 'id']);
                $channel = "calls#".$user->getId();
                if(!empty($userFields['flat_number']))
                    $userFields['actual_address'] .= " - {$userFields['flat_number']}";
                $this->centrifugo->publish($channel, [
                    'message'=>"
<div style=\"font-size:1rem;\"><p>{$command->callerNumber}</p>
<p>{$userFields['full_name']}<br>
{$userFields['actual_address']}</p>
<a class=\" bg-success\" href=\"{$url}\">Отрыть карточку клиента</a><br>
<a class=\" bg-info\" target=\"_blank\" href=\"https://istranet.pro/crm/deal/list/?apply_filter=Y&with_preset=Y&FIND={$command->callerNumber}\">Открыть карточку в bitrix24</a>
</div>",
                ]);
            }
        } catch(\DomainException $e) {
            $this->centrifugo->publish('calls', ['message'=> "<br><a class=\" bg-info\" target=\"_blank\" href=\"https://istranet.pro/crm/deal/list/?apply_filter=Y&with_preset=Y&FIND={$command->callerNumber}\">Открыть карточку в bitrix24</a>"]);
        }
    }
}