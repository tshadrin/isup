<?php
declare(strict_types=1);

namespace App\Controller;

use App\ReadModel\Statistics\OnlineUsersFetcher;
use App\Service\PaymentStatistics\MonthPayments;
use App\Service\Statistics\OnlineUsers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statistics", name="statistic")
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
     * @Route("/show/online-users", name=".online-users.show", methods={"GET"})
     * @IsGranted("ROLE_SUPPORT")
     */
    public function showOnlineUsers(OnlineUsersFetcher $onlineUsersFetcher, \Redis $redis): Response
    {
        if($redis->exists('daily_graphs')) {
            $graphData = (array)json_decode($redis->get('daily_graphs'));
        } else {
            $online_users_records = $onlineUsersFetcher->getOnlineUsersForLastDay();

            for ($i = 0; $i < count($online_users_records); $i++) {
                $formatted_data[$online_users_records[$i]['server']][] = $online_users_records[$i];
            }
            $graphData = [];
            foreach ($formatted_data as $server => $data) {
                $graphData[$server]['hours'] = [];
                $graphData[$server]['counts'] = [];
                foreach ($data as $datum) {
                    $graphData[$server]['hours'][] = $datum['hour'];
                    $graphData[$server]['counts'][] = $datum['count'];
                }
                $graphData[$server]['hours'] = implode(", ", $graphData[$server]['hours']);
                $graphData[$server]['counts'] = implode(", ", $graphData[$server]['counts']);
            }
            $redis->set('daily_graphs', json_encode($graphData), 600);
        }
        return $this->render("Statistics/online-users.html.twig", ['graphData' => $graphData,]);
    }
}
