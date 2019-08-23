<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\PaymentStatistics\MonthPayments\Command;
use App\Service\PaymentStatistics\MonthPayments\Handler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statistic", name="statistic")
 * @IsGranted("ROLE_MODERATOR")
 */
class StatisticsController
{
    /**
     * @Route("/month-payments", name=".month-paymetns", methods={"GET"})
     */
    public function getMonthPayments(Request $request, Handler $handler): Response
    {
        $command = new Command(1,2017);
        $handler->handle($command);
    }

    /**
     * @Route("/month-connected-users", name=".month-conntected-users", methods={"GET"})
     */
    public function getConnectedUsers(Request $request ): Response
    {

    }
}
