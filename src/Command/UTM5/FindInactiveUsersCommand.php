<?php


namespace App\Command\UTM5;

use App\Service\UTM5\URFAService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FindInactiveUsersCommand extends Command
{
    /**
     * @var URFAService
     */
    private $URFAService;

    protected static $defaultName="utm5:inactive-users";

    public function __construct(URFAService $URFAService)
    {
        parent::__construct();
        $this->URFAService = $URFAService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        ini_set('memory_limit', '-1');

        $result = $this->URFAService->getUrfa()->rpcf_search_users_new(array(
            'select_type' => 0,
            'patterns_count' => array(),
        ));

        $current = time();

        foreach ($result['user_data_size'] as $value)
        {
            $info = $this->URFAService->getUrfa()->rpcf_get_userinfo(array(
                'user_id' => $value['user_id']
            ));

            $urfa_user = $this->auth($info['login'], $info['password']);


            $report = $urfa_user->rpcf_user5_payments_report(array(
                'time_start' => $current - 31536000 * 3, // 31536000 - год
                'time_end' => $current
            ));

            $is_active = false;

            for ($i = 0;
                 $i < count($report['accounts_count']);
                 $i++)
            {
                for ($s = 0;
                     $s < count($report['accounts_count'][$i]['atr_size']);
                     $s++)
                {
                    if (floatval($report['accounts_count'][$i]['atr_size'][$s]['payment']) > 0)
                    {
                        $is_active = true;
                        break;
                    }
                }
            }

            if ($is_active === false) {
                $urfa->rpcf_add_group_to_user(array(
                    'user_id' => $value['user_id'],
                    'group_id' => 1111,
                ));

                echo $value['user_id'] . " inactive\n";
            } else {
                echo $value['user_id'] . " active\n";
            }
        }
    }

    private function auth($login, $password) {
        $urfa = \URFAClient::init(array(
            'login'    => $login,
            'password' => $password,
            'address'  => '10.3.7.42',
        ));

        return $urfa;
    }
}