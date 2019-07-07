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
    public function tmpOpen($account, SSHService $SSHService, URFAService $URFAService)
    {
        $user = $URFAService->getUserByAccount($account);
        $result = [];
        foreach ($user->getRouters() as $router) {
            foreach ($user->getIps() as $ip) {
                $result[] = $SSHService->openInternetTemporary($ip, $router->getIp());
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
    public function diagnostic($server, $ip, $command, SSHService $SSHService)
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
     * @param int $account
     * @param SSHService $SSHService
     * @param URFAService $URFAService
     * @return JsonResponse
     * @Route("/ssh/checkturbo/{account}", name="ssh_checkturbo", methods={"GET"}, requirements={"account": "\d+"})
     */
    public function checkTurbo(int $account, SSHService $SSHService, URFAService $URFAService): JsonResponse
    {
        $user = $URFAService->getUserByAccount($account);
        foreach ($user->getRouters() as $router) {
            foreach ($user->getIps() as $ip) {
                if (!is_null($result = $SSHService->checkTurboForUser($ip, $router->getIp()))) {
                    return $this->json(['status' => "TURBO_ENABLED", 'time_left' => $result]);
                }
            }
        }
        return $this->json(['status' => "TURBO_NOT_ENABLED"]);
    }

    /**
     * Включает турбо режим
     * @param int $id - лицевой счет клиента
     * @param int $sid - идентификатор услуги
     * @param SSHService $SSHService
     * @param URFAService $URFAService
     * @return JsonResponse
     * @Route("/ssh/turboopen/{id}/{sid}", name="ssh_turboopen", methods={"GET"}, requirements={"id": "\d+", "sid": "\d+"})
     */
    public function turboOpen(int $id, int $sid, SSHService $SSHService, URFAService $URFAService): JsonResponse
    {
        $user = $URFAService->getUserByAccount($id);
        $checkResult = false;
        // Проверяем на сервере, включена ли опция у пользователя
        foreach ($user->getRouters() as $router) {
            //Проверяем только одну комбинацию
            foreach ($user->getIps() as $ip) {
                if (($checkResult = $SSHService->isTurbo($ip, $router->getIp()))) {
                    break;
                }
            }
        }
        $openResult = [];
        if (!$checkResult) { //Если проверка вернула false
            // Включаем турбо-режим
            foreach ($user->getRouters() as $router) {
                foreach ($user->getIps() as $ip) {
                    array_push($openResult, $SSHService->openTurbo($ip, $router->getIp()));
                }
            }
            // Проверяем включился ли турбо-режим
            if (in_array(false, $openResult)) {
                return $this->json(['status' => "ERROR"]);
            }
            // Проставляем услугу в биллинг
            $urfa = $URFAService->getUrfa();
            $urfa->rpcf_add_once_slink_ex(["user_id" => $user->getId(),
                "account_id" => $id,
                "service_id" => $sid]);
            return $this->json(['status' => "OK"]);
        }
        return $this->json(['status' => "TURBO_ENABLED"]);
    }
}
