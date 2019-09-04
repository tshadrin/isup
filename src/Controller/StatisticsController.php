<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\PaymentStatistics\MonthPayments;
use App\Service\Statistics\OnlineUsers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statistics", name="statistic")
 * @IsGranted("ROLE_MODERATOR")
 */
class StatisticsController extends AbstractController
{
    /**
     * @Route("/month-payments", name=".month-paymetns", methods={"GET"})
     */
    public function getMonthPayments(Request $request, MonthPayments\Handler $handler): Response
    {
        $command = new MonthPayments\Command(1,2017);
        $handler->handle($command);
    }

    /**
     * @Route("/month-connected-users", name=".month-conntected-users", methods={"GET"})
     */
    public function getConnectedUsers(Request $request ): Response
    {

    }

    /**
     * @Route("/add/{server}/{count}", name=".add", methods={"GET"}, requirements={"count": "\d+"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function add(string $server, int $count, OnlineUsers\Add\Handler $handler): Response
    {
        try {
            $command = new OnlineUsers\Add\Command($server, $count);
            $handler->handle($command);
            return $this->json(['result' => 'success']);
        } catch (\DomainException|\InvalidArgumentException $e) {
            return $this->json(['result' => $e->getMessage()]);
        }
    }
}
