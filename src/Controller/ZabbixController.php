<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\Zabbix\Alarm\{ Command, Handler };
use App\Service\Zabbix\Notifier\{ ChatNotifier, EmailNotifier };
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ JsonResponse, Request };
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ZabbixController
 * @package App\Controller
 * @Route("/zabbix", name="zabbix")
 */
class ZabbixController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @Route("/alarm/", name=".alarm", methods={"POST"})
     */
    public function alarm(Request $request, LoggerInterface $logger,
                          EmailNotifier $emailNotifier, ChatNotifier $chatNotifier,
                          Handler $alarmHandler): JsonResponse
    {
        $this->logger = $logger;
        if ($request->request->has('subject') &&
            $request->request->has('message')) {
            $command = new Command(
                $request->request->get('subject'),
                $request->request->get('message')
            );

            $alarm = $alarmHandler->handle($command);

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
