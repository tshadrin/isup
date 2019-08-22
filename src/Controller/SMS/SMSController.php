<?php
declare(strict_types=1);

namespace App\Controller\SMS;

use App\Service\SMS\smscSender;
use App\Form\SMS\SmsTemplateForm;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ JsonResponse, RedirectResponse, Request };
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SMSController
 * @package App\Controller\SMS
 */
class SMSController extends AbstractController
{
    /**
     * @param string $type
     * @param Request $request
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @Route("/sms/send/{type}", defaults={"type": "modem"}, name="sms_send", methods={"GET", "PUT"}, requirements={"type": "smsc|modem|all"})
     */
    public function sendSMS(string $type, Request $request, LoggerInterface $logger): JsonResponse
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

                if("modem" === $type) {
                    $this->sendSMSViaModem($phone,$message);
                }
                if("smsc" === $type) {
                    $this->sendSMSViaSmsc($phone, $message);
                }
                if("all" === $type) {
                    $this->sendSMSViaModem($phone,$message);
                    $this->sendSMSViaSmsc($phone, $message);
                }
                $logger->info("Сообщение \"{$message}\" отправлено на номер {$phone}", ['IP-адрес клиента' => $request->getClientIp()]);
                return new JsonResponse(['message' => 'Сообщение успешно отправлено', 'code' => 0,]);
            } else {
                throw new \Exception("Не заданы входные параметры запроса", 1);
            }
        } catch (\Exception $e) {
            $logger->error($e->getMessage(), ['Код ошибки' => $e->getCode(), 'IP-адрес клиента' => $request->getClientIp()]);
            return new JsonResponse(['message' => $e->getMessage(), 'code' => $e->getCode(),]);
        }
    }

    /**
     * @param Request $request
     * @param smscSender $sender
     * @return RedirectResponse
     * @Route("/sms/sendbytemplate/", name="sms_sendtemplate", methods={"POST"})
     */
    public function sendSmsByTemplate(Request $request, smscSender $sender): RedirectResponse
    {
        $form = $this->createForm(SmsTemplateForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $smsTemplateData = $form->getData();
            if ($form->isValid()) {
                $sender->send($smsTemplateData->getPhone(),$smsTemplateData->getSmsTemplate()->getMessage());
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
}
