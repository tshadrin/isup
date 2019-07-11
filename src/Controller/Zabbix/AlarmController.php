<?php
declare(strict_types=1);

namespace App\Controller\Zabbix;

use App\Service\Zabbix\Command\AlarmCommand;
use App\Service\Zabbix\Handler\AlarmHandler;
use App\Service\Zabbix\Notifier\{ ChatNotifier, EmailNotifier };
use App\Service\Zabbix\ZabbixService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ JsonResponse, Request };
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AlarmZabbixController
 * @package App\Controller\UTM5
 */
class AlarmController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Request $request
     * @param LoggerInterface $logger
     * @param EmailNotifier $emailNotifier
     * @param ChatNotifier $chatNotifier
     * @param AlarmHandler $alarmHandler
     * @return JsonResponse
     * @Route("/zabbix/alarm/", name="alarm_zabbix", methods={"POST"})
     */
    public function alarm(Request $request, LoggerInterface $logger,
                          EmailNotifier $emailNotifier, ChatNotifier $chatNotifier,
                          AlarmHandler $alarmHandler): JsonResponse
    {
        $this->logger = $logger;
        if($request->request->has('subject') && $request->request->has('message')) {
            $command = new AlarmCommand($request->request->get('subject'), $request->request->get('message'));
            $alarm = $alarmHandler->handle($command);
            var_dump($alarm);
            $emailNotifier->notify($alarm);
            $chatNotifier->notify($alarm);
            $logger->info('Message successfully notified');
            return $this->json(['result' => 'success', ]);
        } else {
            $logger->error('Data not provides from request');
            return $this->json(['result' => 'error',  'error' => 'Data not provides from request', ]);
        }
    }
}
