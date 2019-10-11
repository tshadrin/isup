<?php
declare(strict_types=1);

namespace App\Controller\SMS;

use App\Entity\UTM5\UTM5User;
use App\Service\Bitrix\HttpClient;
use App\Service\SMS\SMSCSender;
use App\Form\SMS\SmsTemplateForm;
use App\Service\UTM5\UTM5DbService;
use App\Service\VariableFetcher;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, RedirectResponse, Request, Response};
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SMSController
 * @package App\Controller\SMS
 * @Route("/sms", name="sms")
 */
class SMSController extends AbstractController
{
    /**
     * @param string $type
     * @param Request $request
     * @param LoggerInterface $smsLogger
     * @return JsonResponse
     * @Route("/send/{type}", defaults={"type": "modem"}, name="_send", methods={"GET", "PUT"}, requirements={"type": "smsc|modem|all"})
     */
    public function sendSMS(string $type, Request $request, LoggerInterface $smsLogger): JsonResponse
    {

        try {
            if ($request->query->has('phone') && $request->query->has('message')) {
                $phone = $request->query->filter('phone',
                                              null,
                                                FILTER_VALIDATE_REGEXP,
                                                     ['options' => ['regexp' => '/^9[0-9]{9}/',],]);
                if (false === $phone)
                    throw new \Exception("Номер телефона в неправильном формате", 3);
                $message = $request->query->get('message');
                if (empty($message))
                    throw new \Exception("Попытка отправить пустое сообщение на номер: {$phone}", 2);
                if (mb_strlen($message, 'utf8') > 70)
                    throw new \Exception("Размер сообщения отправляемого на номер {$phone} превышает допустимый", 4);
                $message = addslashes(urldecode($message)); $code = -1; $data = [];

                if ("modem" === $type) {
                    $this->sendSMSViaModem($phone,$message);
                }
                if ("smsc" === $type) {
                    $this->sendSMSViaSmsc($phone, $message);
                }
                if ("all" === $type) {
                    $this->sendSMSViaModem($phone,$message);
                    $this->sendSMSViaSmsc($phone, $message);
                }
                $smsLogger->info("Сообщение \"{$message}\" отправлено на номер {$phone}", ['IP-адрес клиента' => $request->getClientIp()]);
                return new JsonResponse(['message' => 'Сообщение успешно отправлено', 'code' => 0,]);
            } else {
                throw new \Exception("Не заданы входные параметры запроса", 1);
            }
        } catch (\Exception $e) {
            $smsLogger->error($e->getMessage(), ['Код ошибки' => $e->getCode(), 'IP-адрес клиента' => $request->getClientIp()]);
            return new JsonResponse(['message' => $e->getMessage(), 'code' => $e->getCode(),]);
        }
    }

    /**
     * @param Request $request
     * @param SMSCSender $sender
     * @return RedirectResponse
     * @Route("/sendbytemplate", name="_sendtemplate", methods={"POST"})
     * @IsGranted("ROLE_SUPPORT")
     */
    public function sendSmsByTemplate(Request $request,
                                      SMSCSender $sender,
                                      VariableFetcher $variableFetcher,
                                      UTM5DbService $UTM5DbService,
                                      ComputerPasswordGenerator $computerPasswordGenerator,
                                      string $smotreshka): RedirectResponse
    {
        $form = $this->createForm(SmsTemplateForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $smsTemplateData = $form->getData();
                $variableFetcher->setText($smsTemplateData->getSmstemplate()->getMessage());
                if ($variableFetcher->hasVariables()) {
                    try {
                        $utmUser = $UTM5DbService->search((string)$smsTemplateData->getUtmId());
                        $replacements = [ //all posible replacements
                            'login' => $utmUser->getLogin(),
                            'password' => $utmUser->getPassword(),
                            'smotreshka_login' => $this->getLifestreamLogin($smotreshka, $utmUser),
                            'smotreshka_password' => $computerPasswordGenerator->generatePassword(),
                            'balance' => $utmUser->getBalance(),
                            'req_payment' => $utmUser->getRequirementPayment(),
                            'discount_date' => $utmUser->getDiscountDate(),
                        ];
                        $variableFetcher->replaceVariables($replacements);
                        if ($variableFetcher->hasVariable('smotreshka_password')) {
                            $smotreshkaPassword = $variableFetcher->getVariable('smotreshka_password');
                            $hc = new CurlHttpClient();
                            $result = $hc->request("POST", "{$smotreshka}/v2/accounts/{$utmUser->getLifestreamId()}/reset-password", [
                                'body' => json_encode(['password' => $smotreshkaPassword]),
                            ]);
                            $response = json_decode($result->getContent(), true);
                            if (!(array_key_exists('status', $response) && $response['status'] === "ok")) {
                                throw new \DomainException("Status is not ok");
                            }
                        }
                    } catch (\DomainException | \InvalidArgumentException $exception) {
                        $this->addFlash("error", "Sms send error: {$exception->getMessage()}");
                        return $this->redirectToRoute("search.by.data", ['type' => 'id', 'value' => $smsTemplateData->getUtmId()]);
                    }
                }
                $sender->send($smsTemplateData->getPhone(), $variableFetcher->getText());
                $this->addFlash("notice", "Message sended");
            } else {
                $errors = $form->getErrors(true);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
            return $this->redirectToRoute("search.by.data", ['type' => 'id', 'value' => $smsTemplateData->getUtmId()]);
        }
        return $this->redirectToRoute("search");
    }

    private function getLifestreamLogin(string $url, UTM5User $UTM5User): string
    {
        $hc = new CurlHttpClient();
        $rr = $hc->request("GET", "{$url}/v2/accounts/{$UTM5User->getLifestreamId()}");
        $data = (json_decode($rr->getContent(), true));
        return $data["username"];
    }

    /**
     * @param string $phone
     * @param string $message
     */
    private function sendSMSViaModem(string $phone, string $message): void
    {
        exec("/usr/bin/smssend +7{$phone} '{$message}'", $data, $code);
        if (0 !== $code) {
            throw new \DomainException("Ошибка при отправке сообщения через модем на номер {$phone}", 5);
        }
    }

    /**
     * @return Response
     * @Route("/one", name="_one", methods={"GET"})
     */
    public function test(string $smotreshka, UTM5DbService $UTM5DbService, ComputerPasswordGenerator $computerPasswordGenerator): Response
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', '8(925)524-0539');
        if (mb_strlen($phoneNumber) === 11 && $phoneNumber[0] === "8") {
            $phoneNumber = mb_substr($phoneNumber, 1);
        }
        dump($phoneNumber);
        exit;
    }
}
