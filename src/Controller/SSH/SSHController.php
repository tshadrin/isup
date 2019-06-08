<?php

namespace App\Controller\SSH;

use App\Service\SSH\SSHService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UTM5\URFAService;
use Symfony\Component\HttpFoundation\JsonResponse;

class SSHController extends AbstractController
{
    /**
     * На время открыть интернет пользователю по account_id
     * @param $account
     * @param SSHService $SSHService
     * @param URFAService $URFAService
     * @return JsonResponse
     * @Route("/ssh/tmpopen/{account}", name="ssh_tmpopen", methods={"GET"}, requirements={"account": "\d+"})
     */
    public function tmpOpenAction($account, SSHService $SSHService, URFAService $URFAService)
    {
        $user = $URFAService->getUserByAccount($account);
        $result = [];
        foreach ($user->getRouters() as $router) {
            foreach ($user->getIps() as $ip) {
                $result[] = $SSHService->openInternetTemporary($ip, $router['ip']);
            }
        }
        foreach ($result as $v) {
            if (!$v) {
                return $this->json(['status' => "ERROR"]);
            }
        }
        return $this->json(['status' => "OK"]);
    }

    /**
     * Выполнить диагностическую комманду
     * @param $server
     * @param $ip
     * @param $command
     * @param SSHService $SSHService
     * @return JsonResponse
     * @Route("/ssh/diagnostic/{server}/{ip}/{command}", name="ssh_diagnostic", methods={"GET"}, requirements={"command": "ping|kk|kkip|spd|itu|port"})
     */
    public function diagnosticAction($server, $ip, $command, SSHService $SSHService)
    {
        $diag = $SSHService->getConnection($server);
        if ("ping" == $command)
            $data = $diag->ssh_exec("arping -c 3 -I eth1 {$ip}");
        if ("kk" == $command)
            $data = $diag->ssh_exec("kk");
        if ("spd" == $command)
            $data = $diag->ssh_exec("spd {$ip}");
        if ("kkip" == $command)
            $data = $diag->ssh_exec("kk {$ip}");
        if ("itu" == $command) {
            $data = $diag->ssh_exec("/usr/local/bin/check_traff {$ip}");
        }
        if ("turbo" == $command) {
            $data = $diag->ssh_exec("ipset -L TURBO | grep -w {$ip}");
        }
        if ("mail" == $command) {
            $data = $diag->ssh_exec("ipset -L MAIL | grep -w {$ip}");
        }
        if(empty($data)) { $data = "Нет данных."; }
        return $this->json(['ping_data' => $data,]);
    }

    /**
     * Проверка турбо-режима по account_id
     * @param $account
     * @param SSHService $SSHService
     * @param URFAService $URFAService
     * @return JsonResponse
     * @Route("/ssh/checkturbo/{account}", name="ssh_checkturbo", methods={"GET"}, requirements={"account": "\d+"})
     */
    public function checkTurboAction($account, SSHService $SSHService, URFAService $URFAService)
    {
        $user = $URFAService->getUserByAccount($account);
        $result = [];

        foreach ($user->getRouters() as $router)
            foreach ($user->getIps() as $ip) {
                array_push($result, $SSHService->checkTurboForUser($ip, $router['ip']));
                break;
            }
        foreach ($result as $v) {
            if (!$v)
                return $this->json(['status' => "TURBO_NOT_ENABLED"]);
        }
        return $this->json(['status' => "TURBO_ENABLED", 'time_left' => $result[0]]);
    }

    /**
     * Включает турбо режим
     * @param $id - лицевой счет клиента
     * @param $sid - идентификатор услуги
     * @param SSHService $SSHService
     * @param URFAService $URFAService
     * @return JsonResponse
     * @Route("/ssh/turboopen/{id}/{sid}", name="ssh_turboopen", methods={"GET"}, requirements={"id": "\d+", "sid": "\d+"})
     */
    public function turboOpenAction($id, $sid, SSHService $SSHService, URFAService $URFAService)
    {
        $user = $URFAService->getUserByAccount($id);
        $result = [];

        // Проверяем на сервере, включена ли опция у пользователя
        foreach ($user->getRouters() as $router)
            //Проверяем только одну комбинацию
            foreach ($user->getIps() as $ip) {
                array_push($result, $SSHService->checkTurboForUser($ip, $router['ip']));
                break;
            }

        $result2 = [];
        foreach ($result as $v) {
            if (!$v) { // Если нет значений в массиве, значит турбо-режим не включен
                // Включаем турбо-режим
                foreach ($user->getRouters() as $router)
                    foreach ($user->getIps() as $ip)
                        array_push($result2, $SSHService->openTurbo($ip, $router['ip']));

                // Проверяем включился ли турбо-режим
                foreach ($result2 as $val)
                    if (!$val)
                        return $this->json(['status' => "ERROR"]);
                // Проставляем услугу в биллинг
                $urfa = $URFAService->getUrfa();
                $urfa->rpcf_add_once_slink_ex(["user_id" => $user->getId(),
                                               "account_id" => $id,
                                               "service_id" => $sid]);
                return $this->json(['status' => "OK"]);
            }
        }
        return $this->json(['status' => "TURBO_ENABLED", 'time_left' => $result[0]]);
    }
}
