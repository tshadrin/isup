<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\Zabbix\Alarm;
use InvalidArgumentException;
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

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/alarm/", name=".alarm", methods={"GET","POST"})
     */
    public function alarm(Request $request, Alarm\Handler $handler): JsonResponse
    {
        try {
            $command = new Alarm\Command(
                $request->request->get('subject', ""),
                $request->request->get('message', "")
            );

            $handler->handle($command);
        } catch (InvalidArgumentException $e) {
            $this->logger->error("Bad arguments", ['message' => $e->getMessage(),]);
            return $this->json(['result' => "error",]);
        }

        $this->logger->info("Message successfully processed");
        return $this->json(['result' => "success",]);
    }
}
