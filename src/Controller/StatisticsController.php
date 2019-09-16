<?php
declare(strict_types=1);

namespace App\Controller;

use App\ReadModel\DateIntervalTransformer;
use App\Service\Statistics\OnlineUsers;
use App\Service\Statistics\Payments\Monthly\PaymentsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statistics", name="statistics")
 */
class StatisticsController extends AbstractController
{
    /**
    #!/bin/sh
    COUNTS=`/arhiv/arhiv/remote_exec 'arp -en -i eth1 |grep ether|grep -Ev "172\.1[7-9]|172\.2[0-1]"|wc -l' | grep -v ssh`
    SERVERS="26 27 34 35 37 39 40 44 45 48 49 50 51 52 53 54 55 57 58 59 60 61 62 67 68"
    SERVERS=$(echo $SERVERS | tr -s \  '\n')
    set -- $COUNTS
    for j in $SERVERS; do
    curl "https://mon.istranet.ru/statistics/add/""$j/$1" > /dev/null
    shift
    done
     * @Route("/add/{server}/{count}", name=".add", methods={"GET"}, requirements={"server":"\d+","count": "\d+"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function add(int $server, int $count, OnlineUsers\Add\Handler $handler): Response
    {
        try {
            $command = new OnlineUsers\Add\Command($server, $count);
            $handler->handle($command);
            return $this->json(['result' => 'success']);
        } catch (\DomainException|\InvalidArgumentException $e) {
            return $this->json(['result' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/show/online-users", name=".show.online-users", methods={"GET"})
     * @IsGranted("ROLE_SUPPORT")
     */
    public function showOnlineUsers(OnlineUsers\Show\OnlineUsersService $onlineUsersService): Response
    {
        $graphData = $onlineUsersService->getForLastDayGraphData();
        return $this->render("Statistics/online-users.html.twig", ['graphData' => $graphData,]);
    }

    /**
     * @Route("/show/online-users-for-day", name=".show.online-for-day", methods={"GET"})
     * @IsGranted("ROLE_SUPPORT")
     */
    public function showOnlineUsersForDay(Request $request, OnlineUsers\Show\OnlineUsersService $onlineUsersService): Response
    {
        try {
            $command = new OnlineUsers\Show\ForDayCommand($request->query->get("date", (new \DateTime())->format("d-m-Y")));
            $graphData = $onlineUsersService->getForSelectedDayGraphData($command);
        } catch (\DomainException | \InvalidArgumentException $e) {
            $this->addFlash("error", $e->getMessage());
        }
        return $this->render("Statistics/online-users.html.twig", ['graphData' => $graphData ?? [],]);
    }

    /**
 * @Route("/show/online-users-for-week", name=".show.online-for-week", methods={"GET"})
 * @IsGranted("ROLE_SUPPORT")
 */
    public function showOnlineUsersForWeek(Request $request, OnlineUsers\Show\OnlineUsersService $onlineUsersService): Response
    {
        $transformer = DateIntervalTransformer::factory();
        $interval = $transformer->reverseTransform(
            $request->query->get("week", $transformer->transform([
                    (new \DateTimeImmutable())->modify("monday this week"),
                    (new \DateTimeImmutable())->modify("monday this week")->modify("+6 days")])
            )
        );
        try {
            $command = new OnlineUsers\Show\ForWeekCommand($interval);
            $graphData = $onlineUsersService->getForSelectedWeekGraphData($command);
        } catch (\DomainException | \InvalidArgumentException $e) {
            $this->addFlash("error", $e->getMessage());
        }
        return $this->render("Statistics/online-users.html.twig", ['graphData' => $graphData ?? [],]);
    }
    /**
     * @Route("/show/online-users-for-month", name=".show.online-for-month", methods={"GET"})
     * @IsGranted("ROLE_SUPPORT")
     */
    public function showOnlineUsersForMonth(Request $request, OnlineUsers\Show\OnlineUsersService $onlineUsersService): Response
    {
        try {
            $command = new OnlineUsers\Show\ForDayCommand($request->query->get("date", (new \DateTime())->format("d-m-Y")));
            $graphData = $onlineUsersService->getForSelectedDayGraphData($command);
        } catch (\DomainException | \InvalidArgumentException $e) {
            $this->addFlash("error", $e->getMessage());
        }
        return $this->render("Statistics/online-users.html.twig", ['graphData' => $graphData ?? [],]);
    }

    /**
     * @Route("/show/online-users-hourly", name=".show.online-users-hourly", methods={"GET"})
     * @IsGranted("ROLE_SUPPORT")
     */
    public function showOnlineUsersHourly(OnlineUsers\Show\OnlineUsersService $onlineUsersService): Response
    {
        $graphData = $onlineUsersService->getForLastHoursGraphData();
        return $this->render("Statistics/online-users.html.twig", ['graphData' => $graphData, 'hourly'=>true]);
    }

    /**
     * @Route("/show/payments-by-server", name=".show.payments-by-server", methods={"GET"})
     * @IsGranted("ROLE_SUPPORT")
     */
    public function showMonthlyPaymentsByServer(PaymentsService $paymentService): Response
    {
        $graphData = $paymentService->getMonthlyForLastYearGraphData();
        return $this->render("Statistics/payments.html.twig", ['graphData' => $graphData,]);
    }
}
