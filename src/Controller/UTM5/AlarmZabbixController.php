<?php
declare(strict_types=1);

namespace App\Controller\UTM5;

use App\Service\Zabbix\{ ChatNotifier, EmailNotifier, ZabbixService };
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ JsonResponse, Request };
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AlarmZabbixController
 * @package App\Controller\UTM5
 */
class AlarmZabbixController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Request $request
     * @param LoggerInterface $logger
     * @param ZabbixService $zabbixService
     * @param EmailNotifier $emailNotifier
     * @param ChatNotifier $chatNotifier
     * @return JsonResponse
     * @Route("/zabbix/alarm/", name="alarm_zabbix", methods={"POST"})
     */
    public function alarmAction(Request $request, LoggerInterface $logger,
                                ZabbixService $zabbixService, EmailNotifier $emailNotifier,
                                ChatNotifier $chatNotifier): JsonResponse
    {
        $this->logger = $logger;
        if($request->request->has('subject') && $request->request->has('message')) {
            $message = $zabbixService->handle($request->request->get('subject'), $request->request->get('message'));
            $emailNotifier->notify($message);
            $chatNotifier->notify($message);
            $logger->info('Message successfully notified', [
                'text' => $message->getText(),
                'letter' => $message->getLetter(),
                'subject' => $message->getSubject(),
                ]);
            return $this->json(['result' => 'success']);
        } else {
            $logger->error('Data not provides from request');
            return $this->json([
                'result' => 'error',
                'error' => 'Data not provides from request'
            ]);
        }
    }
}
