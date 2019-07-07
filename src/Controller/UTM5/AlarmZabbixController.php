<?php

namespace App\Controller\UTM5;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\UTM5\UTM5User;
use App\Service\UTM5\BitrixRestService;
use App\Service\UTM5\UTM5DbService;
use Symfony\Component\Routing\Annotation\Route;


class AlarmZabbixController extends AbstractController
{
    protected $logger;

    /**
     * @param Request $request
     * @param BitrixRestService $bitrix_rest_service
     * @param UTM5DbService $UTM5_db_service
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/zabbix/alarm/", name="alarm_zabbix", methods={"GET", "POST"})
     */
    public function alarm(Request $request,
                                BitrixRestService $bitrix_rest_service,
                                UTM5DbService $UTM5_db_service,
                                LoggerInterface $logger)
    {
        $this->logger = $logger;
        if($request->request->has('subject') && $request->request->has('message')) {
            $subject = $this->replaceSpecialChars($request->request->get("subject"));
            $message_body = $this->replaceSpecialChars($request->request->get("message"));
            $logger->info("Сообщение от Zabbix", ['subject' => $subject, 'message' => $message_body]);
            list($message, $letter) = explode('$$', $message_body);
            if(isset($message) && isset($letter)){
                $message = trim($message);
                $letter = trim($letter);
                list($message_start, $utm5_id, $message_end) = explode('$', $message);
                if(isset($message_start) && isset($utm5_id) && isset($message_end)) {
                    $url = $this->generateUrl('search',
                        ['type' => 'id', 'value' => $utm5_id],
                        UrlGeneratorInterface::ABSOLUTE_URL);
                    $chat_link = "[url={$url}]ID пользователя: {$utm5_id}[/url]";
                    $message = "{$message_start}\${$chat_link}\${$message_end}";

                    $user = $UTM5_db_service->search($utm5_id, 'id');
                    if($user instanceof UTM5User) {
                        $message .= "\n".$user->getFullName();
                        if(!empty($user->getEmail())) {
                            $message .= "\nПисьмо с оповещением отправлено клиенту.";
                            $this->sendEmail($user->getEmail(), "ООО Истранет. Автоматическое оповещение о проблеме", $letter);
                        } else {
                            $message .= "\nПисьмо не отправлено. Почта у пользователя не заполнена.";
                            $logger->info("Письмо не отправлено. Почта у клиента не задана.");
                        }
                    } else {
                        $logger->info("Пользователь не найден.");
                    }

                } else {
                    $logger->info("Не указан id пользователя в сообщении от zabbix", ['subject' => $subject, 'message' => $message_body]);
                }
            } else {
                $message = trim($message_body);
                $logger->error("Нет разделителя сообщения и письма. Сообщение отправлено только в чат");
            }
            if ($bitrix_rest_service->sendToChat("{$subject}\n{$message}"))
                $logger->info("Сообщение успешно отправлено в чат.", ['subject' => $subject, 'message' => $message]);
            else
                $logger->error("Сообщение в чат не отправлено");
            return $this->json(['result' => 'success']);
        } else {
            $logger->error("Тема и сообщение не заданы");
            return $this->json(['result' => 'error']);
        }
    }

    /**
     * Очистка строки от спецсимволов
     * @param $message
     * @return string
     */
    function replaceSpecialChars($message) {
        return html_entity_decode(htmlspecialchars_decode($message));
    }

    /**
     * Отправка письма
     * @param $email
     * @param $subject
     * @param $message
     * @param \Swift_Mailer $mailer
     */
    function sendEmail($email, $subject, $message) {
        $mailer = $this->get('mailer');
        $letter = (new \Swift_Message($subject))
            ->setFrom('no-reply@istranet.ru')
            ->setTo($email)
            ->setBody($message);
//                ->setBody(
//                    $this->renderView(
//                    // app/Resources/views/Emails/registration.html.twig
//                        'Emails/registration.html.twig',
//                        ['name' => $name]
//                    ),
//                    'text/html'
//                )
        $mailer->send($letter);
        $this->logger->info('Оповещение отправлено на почту',
            ['email' => $email, 'subject' => $subject, 'message' => $message,]);
    }
}
