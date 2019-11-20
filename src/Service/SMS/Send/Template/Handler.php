<?php
declare(strict_types=1);


namespace App\Service\SMS\Send\Template;


use App\Entity\UTM5\UTM5User;
use App\Form\SMS\SmsTemplateDTO;
use App\Service\SMS\SenderInterface;
use App\Service\UTM5\UTM5DbService;
use App\Service\VariableFetcher;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Handler
{
    /** @var VariableFetcher  */
    private $variableFetcher;
    /** @var UTM5DbService  */
    private $UTM5DbService;
    /** @var ComputerPasswordGenerator  */
    private $computerPasswordGenerator;
    /** @var string  */
    private $smotreshka;
    /** @var SenderInterface  */
    private $sender;
    /** @var HttpClientInterface  */
    private $httpClient;

    public function __construct(VariableFetcher $variableFetcher,
                                UTM5DbService $UTM5DbService,
                                ComputerPasswordGenerator $computerPasswordGenerator,
                                string $smotreshka,
                                SenderInterface $sender,
                                HttpClientInterface $httpClient)
    {
        $this->variableFetcher = $variableFetcher;
        $this->UTM5DbService = $UTM5DbService;
        $this->computerPasswordGenerator = $computerPasswordGenerator;
        $this->smotreshka = $smotreshka;
        $this->sender = $sender;
        $this->httpClient = $httpClient;
    }

    public function handle(SmsTemplateDTO $smsTemplateData): void
    {
        $this->variableFetcher->setText($smsTemplateData->getSmstemplate()->getMessage());
        if ($this->variableFetcher->hasVariables()) {
            $utmUser = $this->UTM5DbService->search((string)$smsTemplateData->getUtmId());
            $replacements = [ //all posible replacements
                'login' => $utmUser->getLogin(),
                'password' => $utmUser->getPassword(),
                'smotreshka_login' => $this->getLifestreamLogin($this->smotreshka, $utmUser),
                'smotreshka_password' => $this->computerPasswordGenerator->generatePassword(),
                'balance' => $utmUser->getBalance(),
                'req_payment' => $utmUser->getRequirementPayment(),
                'discount_date' => $utmUser->getDiscountDate(),
            ];
            $this->variableFetcher->replaceVariables($replacements);
            if ($this->variableFetcher->hasVariable('smotreshka_password')) {
                $this->applySmotreshkaPassword(
                    $this->variableFetcher->getVariable('smotreshka_password'),
                    $utmUser->getLifestreamId()
                );
            }
        }
        $this->sender->send($smsTemplateData->getPhone(), $this->variableFetcher->getText());
    }

    private function getLifestreamLogin(string $url, UTM5User $UTM5User): string
    {
        if(is_null($UTM5User->getLifestreamId())) {
            return '';
        }
        $response = $this->httpClient->request(
            "GET",
            "{$url}/v2/accounts/{$UTM5User->getLifestreamId()}"
        );
        $data = json_decode($response->getContent(), true);
        if(!array_key_exists('username', $data)) {
            throw new \DomainException("Error account request.");
        }
        return $data["username"];
    }

    private function applySmotreshkaPassword(string $password, $userId): void
    {
        $response = $this->httpClient->request(
            "POST",
            "{$this->smotreshka}/v2/accounts/{$userId}/reset-password",
            ['body' => json_encode(['password' => $password]),]
        );
        $data = json_decode($response->getContent(), true);
        if (!(array_key_exists('status', $data) && $data['status'] === "ok")) {
            throw new \DomainException("Password not apply. Change password request error");
        }
    }
}