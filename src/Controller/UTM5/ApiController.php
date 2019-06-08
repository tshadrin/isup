<?php

namespace App\Controller\UTM5;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UTM5\UTM5User;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UTM5\BitrixRestService;
use App\Service\UTM5\UTM5DbService;
use App\Service\UTM5\URFAService;
use Symfony\Component\HttpFoundation\JsonResponse;


class ApiController extends AbstractController
{
    /**
     * Создание пользователя у UTM5 на основании запросов из битрикс
     * @param Request $request
     * @param BitrixRestService $bitrix_rest_service
     * @param URFAService $URFAService
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @Route("/api/bitrixcreateuser/", name="bitrix_create_user", methods={"GET", "POST"})
     */
    public function createUTM5UserAction(Request $request,
                                         BitrixRestService $bitrix_rest_service,
                                         URFAService $URFAService,
                                         LoggerInterface $logger)
    {
        if($request->request->has('document_id')) {
            $did = $request->request->get('document_id');
            $tmp_login = $did[2]; //DEAL_<NUM>
            $deal_id = explode('_', $tmp_login); //DEAL <NUM>
            $data = $bitrix_rest_service->getDeal($deal_id[1]); // данные о сделке ['address', 'id', 'name', 'phone', 'utm5_id']
            $uid = $URFAService->addUser($tmp_login, $data['phone'], $data['address'], $data['name']);
            $bitrix_rest_service->updateDeal($deal_id[1], ['UF_CRM_5B3A2EC6DC360' => $uid,]);
            return $this->json(["success" =>  'deal updated']);
        } else {
            $logger->error('Переменная document_id не задана');
            return $this->json(['error' => 'document_id not found']);
        }
    }

    /**
     * Удаление пользователя в биллинге если клиент отказался
     * @param Request $request
     * @param BitrixRestService $bitrixRestService
     * @param URFAService $URFAService
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @Route("/api/bitrixremoveuser/", name="bitrix_remove_user", methods={"GET", "POST"})
     */
    public function  removeUTM5UserAction(Request $request,
                                          BitrixRestService $bitrixRestService,
                                          URFAService $URFAService,
                                          LoggerInterface $logger)
    {
        if($request->request->has('document_id')) {
            $did = $request->request->get('document_id');
            $tmp_login = $did[2]; //DEAL_<NUM>
            $deal_id = explode('_', $tmp_login); //DEAL <NUM>
            $data = $bitrixRestService->getDeal($deal_id[1]); // данные о сделке ['address', 'id', 'name', 'phone', 'utm5_id']
            $logger->info("DATA DEAL", $data);
            if(!empty($data['utm5_id'])) {
                $uid = $URFAService->removeUser($data['utm5_id']);
                $bitrixRestService->updateDeal($deal_id[1], ['UF_CRM_5B3A2EC6DC360' => 0,]);
                $logger->info("User {$data['utm5_id']} удален ", $uid);
            }
            return $this->json(['success' => 'user removed']);
        } else {
            $logger->error('Переменная document_id не задана при удалении');
            return $this->json(['error' => 'document_id not found']);
        }
    }

    /**
     * Проверка оплаты клиентом подключения к сети
     * @param Request $request
     * @param BitrixRestService $bitrix_rest_service
     * @param UTM5DbService $UTM5_db_service
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @throws \Exception
     * @Route("/api/paycheck/", name="bitrix_check_payments", methods={"GET", "POST"})
     */
    public function checkUTM5PaymentsAction(Request $request,
                                            BitrixRestService $bitrix_rest_service,
                                            UTM5DbService $UTM5_db_service,
                                            LoggerInterface $logger)
    {
        if($request->request->has('document_id')) {
            $did = $request->request->get('document_id');
            $tmp_login = $did[2]; //DEAL_<NUM>
            $deal_id = explode('_', $tmp_login); //DEAL <NUM>
            $result = $bitrix_rest_service->getBitrixData('crm.deal.get', ["ID" => $deal_id[1],]);
            if (array_key_exists('result', $result) && array_key_exists('STAGE_ID', $result['result'])) {
                if(array_key_exists('UF_CRM_5B3A2EC6DC360', $result['result']) && (!empty($result['result']['UF_CRM_5B3A2EC6DC360']))) {
                    $user = $UTM5_db_service->search($result['result']['UF_CRM_5B3A2EC6DC360']);
                    if($user instanceof UTM5User) {
                        $payments = $user->getPayments();
                        if(is_array($payments)) {
                            $amount = 0;
                            foreach($payments as $payment) {
                                if($payment['amount'] > 0)
                                    $amount += $payment['amount'];
                            }
                            if($amount > 0) {
                                $status = $result['result']['STAGE_ID'];
                                $status = explode(':', $status);
                                $new_status = "{$status[0]}:WON";
                                $bitrix_rest_service->updateDeal($deal_id[1], ["STAGE_ID" => $new_status,]);
                                $logger->info("Статус задачи {$deal_id[1]} изменен");
                                return $this->json(['result' => 'success']);
                            }
                        } else {
                            $logger->error("Пользователь {$result['result']['UF_CRM_5B3A2EC6DC360']} не проводил оплату");
                        }
                    } else {
                        $logger->error("Пользователь {$result['result']['UF_CRM_5B3A2EC6DC360']} не найден");
                    }
                } else {
                    $logger->error("Для сделки не указан id пользователя", $result);
                }
            } else {
                $logger->error("Данные сделки не были получены", $_POST);
            }
        } else {
            $logger->error("Не корректно были переданы данные сделки", $_POST);
        }
        return $this->json(['result' => 'error']);
    }
}
