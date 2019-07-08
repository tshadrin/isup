<?php
declare(strict_types=1);

namespace App\Controller\UTM5;

use App\Entity\UTM5\UTM5User;
use App\Service\UTM5\{ BitrixRestService, UTM5DbService, URFAService };
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ JsonResponse, Request };
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ApiController
 * @package App\Controller\UTM5
 */
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
    public function createUTM5User(Request $request,
                                   BitrixRestService $bitrix_rest_service,
                                   URFAService $URFAService,
                                   LoggerInterface $logger): JsonResponse
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
    public function  removeUTM5User(Request $request,
                                    BitrixRestService $bitrixRestService,
                                    URFAService $URFAService,
                                    LoggerInterface $logger): JsonResponse
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
    public function checkUTM5Payments(Request $request,
                                      BitrixRestService $bitrix_rest_service,
                                      UTM5DbService $UTM5_db_service,
                                      LoggerInterface $logger): JsonResponse
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

    /**
     * Меняет статус Интернета пользователя
     * и перенаправляет на его профиль
     * @param int $id  - id клиента
     * @param URFAService $URFA_service
     * @return JsonResponse
     * @Route("/urfa/change-remindme/{id}/", name="utm_change_remindme", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function changeRemindMe(int $id, URFAService $URFA_service): JsonResponse
    {
        try{
            $URFA_service->changeRemindMe($id);
            return $this->json(['result' => 'success']);
        } catch(\Exception $e)
        {
            return $this->json(['result' => 'error']);
        }

    }

    /**
     * Меняет статус Интернета пользователя
     * и перенаправляет на его профиль
     * @param int $id  - id клиента
     * @param URFAService $URFA_service
     * @return JsonResponse
     * @Route("/urfa/change-intstatus/{id}/", name="utm_change_intstatus", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function changeStatus(int $id, URFAService $URFA_service): JsonResponse
    {
        try {
            if($URFA_service->changeInternetStatus($id))
                return $this->json(['result' => 'success']);
        } catch(\Exception $e) {
            return $this->json(['result' => 'error']);
        }
    }

    /**
     * Обработка запроса на изменение значения полей в карточке клиента
     * @param Request $request
     * @param URFAService $URFAService
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @Route("/urfa/change-editable-filed/", name="user_change_editable_field", methods={"POST"})
     */
    public function changeEditableField(Request $request,
                                        URFAService $URFAService,
                                        TranslatorInterface $translator): JsonResponse
    {
        try {
            if ($request->request->has('name') &&
                $request->request->has('value') &&
                $request->request->has('pk')) {

                $field = $request->request->filter(
                    'name', [],
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/mobile_phone/',],]
                );
                switch ($field) {
                    case 'mobile_phone':
                        $phone_number =  mb_ereg_replace('\D+', '', $request->request->get('value'));
                        $phone_number_len = mb_strlen($phone_number);
                        if(($phone_number_len < 10 && $phone_number_len > 0) || $phone_number_len > 11)
                            return $this->json(['result' => 'error', 'message' => $translator->trans('Phone number must contains 10 digits'),]);
                        if(11 === $phone_number_len) {
                            if(mb_ereg_match('8[0-9]', $phone_number)) {
                                $phone_number = mb_substr($phone_number, 1);
                                $URFAService->editMobilePhoneField(
                                    $phone_number,
                                    $request->request->getInt('pk')
                                );
                                return $this->json(['result' => 'success', 'message' => $translator->trans('Number edit success'),]);
                            } else {
                                return $this->json(['result' => 'error', 'message' => $translator->trans('Incorrect phone number'),]);
                            }
                        }
                        if(10 === $phone_number_len) {
                            if(mb_ereg_match('[0-7,9]', $phone_number)) {
                                $URFAService->editMobilePhoneField(
                                    $phone_number,
                                    $request->request->getInt('pk')
                                );
                                return $this->json(['result' => 'success', 'message' => $translator->trans('Number edit success'),]);
                            } else {
                                return $this->json(['result' => 'error', 'message' => $translator->trans('Incorrect phone number'),]);
                            }
                        }
                        if(0 === $phone_number_len) {
                            $URFAService->editMobilePhoneField(
                                $request->request->get('value'),
                                $request->request->getInt('pk')
                            );
                            return $this->json(['result' => 'success', 'message' => $translator->trans('Phone number is clear')]);
                        }
                        break;
                }
            } else {
                return $this->json(['result' => 'error', 'message' => $translator->trans('Request data not found'),]);
            }
        } catch (\Exception $e) {
            return $this->json(['result' => 'error', 'message' => $e->getMessage(),]);
        }
    }
}
