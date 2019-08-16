<?php


namespace App\Command\UTM5;

use Symfony\Component\Console\Command\Command;

class FindInactiveUsersCommand extends Command
{
    protected function
    {
        ini_set('memory_limit', '-1');

        $result = $urfa->rpcf_search_users_new(array(
            'select_type' => 0,
            'patterns_count' => array(),
        ));

        $current = time();

        foreach ($result['user_data_size'] as $value)
        {
            $info = $urfa->rpcf_get_userinfo(array(
                'user_id' => $value['user_id']
            ));

            $urfa_user = auth($info['login'], $info['password']);


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
}