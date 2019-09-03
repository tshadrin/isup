<?php
declare(strict_types=1);

namespace App\Service\UTM5\CallRegister;



use App\Entity\User\User;
use App\ReadModel\UTM5\UserFetcher;
use App\Repository\UserRepository;
use App\Service\UTM5\URFAService;
use phpcent\Client;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;

class Handler
{
    const CALL_CHANNEL_NAME = 'calls';
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
    /**
     * @var EngineInterface
     */
    private $templating;
    private $command;
    private $userFields;
    /**
     * @var URFAService
     */
    private $URFAService;

    public function __construct(Client $centrifugo,
                                UserFetcher $userFetcher,
                                UrlGeneratorInterface $urlGenerator,
                                TokenStorageInterface $tokenStorage,
                                UserRepository $userRepository,
                                EngineInterface $templating,
                                URFAService $URFAService)
    {
        $this->centrifugo = $centrifugo;
        $this->centrifugo->setSafety(false); //нужно только для теста (
        $this->userFetcher = $userFetcher;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
        $this->templating = $templating;
        $this->URFAService = $URFAService;
    }

    public function handle(Command $command): void
    {
        $this->command = $command;
        $user = $this->getUser();

        try {
            $this->userFields = $this->getUTM5UserData();
            $this->publish($user->getId(), $this->renderUserCard());
        } catch(\DomainException $e) {
            $this->publish($user->getId(), $this->renderNoUserCard());
        }
    }

    private function getUser(): User
    {
        $user = $this->userRepository->findByInternalNumber($this->command->operatorNumber);
        if (is_null($user)) {
            throw new NotFoundHttpException("User with internal number {$this->command->operatorNumber} not found.");
        }
        return $user;
    }

    private function getUTM5UserData(): array
    {
        $data = $this->userFetcher->getUserByPhone(mb_substr($this->command->callerNumber, 2));
        if(!empty($data['flat_number']))
            $data['actual_address'] .= " - {$data['flat_number']}";
        return $data;
    }

    private function publish(int $userId, string $message): void
    {
        $this->centrifugo->publish(self::CALL_CHANNEL_NAME. "#" . $userId, ['message'=> $message, 'title'=> $this->getTitle()]);
    }

    private function renderUserCard(): string
    {
        return $this->templating->render("widget/register-call/user-card.html.twig", [
                'callerNumber' => $this->command->callerNumber,
                'userId' => $this->userFields['id'],
                'userAddress' => $this->userFields['actual_address'],
                'userFullName' => $this->userFields['full_name'],
                'userRequirementPayment' => $this->URFAService->getRequirementPaymentForUser($this->userFields['basic_account']),
                'monCardUrl' => $this->urlGenerator->generate("search.by.data", ["value" => $this->userFields['id'], "type" => 'id']),
                'bitrixCardUrl' => "https://istranet.pro/crm/deal/list/?apply_filter=Y&with_preset=Y&FIND={{$this->command->callerNumber}}",
            ]
        );
    }

    private function renderNoUserCard(): string
    {
        return $this->templating->render("widget/register-call/no-user-card.html.twig",  [
            'callerNumber' => $this->command->callerNumber,
            'bitrixCardUrl' => "https://istranet.pro/crm/deal/list/?apply_filter=Y&with_preset=Y&FIND={{$this->command->callerNumber}}"
        ]);
    }
    private function getTitle(): string
    {
        return "<span  style=\"font-size:1.2rem;\">Входящий звонок</span>";
    }
}