<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\SMS\SmsTemplateForm;
use App\Service\SMS\Send\Template\Handler;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, RedirectResponse, Request, Response};
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
     * @Route("/sendbytemplate", name="_sendtemplate", methods={"POST"})
     * @IsGranted("ROLE_SUPPORT")
     */
    public function sendSmsByTemplate(Request $request, Handler $handler): RedirectResponse
    {
        $form = $this->createForm(SmsTemplateForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try{
                    $handler->handle($form->getData());
                    $this->addFlash("notice", "Message sended");
                } catch (\DomainException | \InvalidArgumentException $exception) {
                    $this->addFlash("error", "Sms send error: {$exception->getMessage()}");
                    return $this->redirectToRoute("search.by.data", ['type' => 'id', 'value' => $form->getData()->getUtmId()]);
                }
            } else {
                $errors = $form->getErrors(true);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
            return $this->redirectToRoute("search.by.data", ['type' => 'id', 'value' => $form->getData()->getUtmId()]);
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
