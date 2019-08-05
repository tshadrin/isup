<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\UTM5\UTM5User;
use App\Service\Bitrix\BitirixCalService;
use App\Service\Bitrix\User\{ Command, Handler };
use App\Service\UTM5\{ BitrixRestService, URFAService, UTM5DbService };
use App\Security\Voter\Bitrix\UserAccess;
use DomainException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ JsonResponse, Request };
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BitrixController
 * @package App\Controller
 * @Route("/api", name="bitrix")
 */
class BitrixController extends AbstractController
{
    /**
     * @return JsonResponse
     * @Route("/getbitrixcal", name=".get.calendar", methods={"GET"})
     */
    public function getCalendar(BitirixCalService $bitirixCalendarService): JsonResponse
    {
        return $this->json($bitirixCalendarService->getActualCallEvents());
    }

    /**
     * @return JsonResponse
     * @Route("/bitrix-user-get", name=".get.user-by-phone", methods={"GET"})
     */
    public function getUserInfo(Request $request, Handler $handler): JsonResponse
    {
        try {
            $command = new Command($request->query->get('phone'));

            $userFields = $handler->handle($command);
        } catch (DomainException | InvalidArgumentException $e) {
            return $this->json(['result' => 'error', 'message' => $e->getMessage()]);
        }

        $response  = $this->json(['result' => 'success', 'data' => $userFields,]);
        $response->headers->set('Access-Control-Allow-Origin', 'https://istranet.pro');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Methods', 'GET');
        $response->headers->set('Access-Control-Allow-Headers',
            'Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control');
        return $response;
    }

    /**
     * Создание пользователя у UTM5 на основании запросов из битрикс
     * @param Request $request
     * @param BitrixRestService $bitrix_rest_service
     * @param URFAService $URFAService
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @IsGranted(UserAccess::CREATE, subject="request")
     * @Route("/bitrixcreateuser/", name=".add.user", methods={"POST"})
     */
    public function addUTM5User(Request $request,
                                   BitrixRestService $bitrix_rest_service,
                                   URFAService $URFAService,
                                   LoggerInterface $logger): JsonResponse
    {
        $command = new \App\Service\Bitrix\User\Create\Command($request->request->get('document_id'));
        if($request->request->has('document_id')) {
            $logger->info("Bitrix request", $request->request->get('document_id'));
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
     * @IsGranted(UserAccess::REMOVE, subject="request")
     * @Route("/bitrixremoveuser/", name=".remove.user", methods={"GET", "POST"})
     */
    public function  deleteUTM5User(Request $request,
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
     * @param Request $request
     * @param BitrixRestService $bitrix_rest_service
     * @param UTM5DbService $UTM5_db_service
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @IsGranted(UserAccess::PAYCHECK, subject="request")
     * @Route("/paycheck/", name=".pay-check.user", methods={"GET", "POST"})
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

}
