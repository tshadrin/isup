<?php
declare(strict_types=1);

namespace App\Controller;

use App\Security\Voter\Bitrix\UserAccess;

use App\Service\Bitrix\Calendar\CalendarInterface;
use App\Service\Bitrix\User\Paycheck;
use App\Service\UTM5\{ BitrixRestService, URFAService };
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
     * @param CalendarInterface $calendar
     * @return JsonResponse
     * @throws \Exception
     * @Route("/getbitrixcal", name=".get.calendar", methods={"GET"})
     */
    public function getCalendar(CalendarInterface $calendar): JsonResponse
    {
        $events = $calendar->getActualEvents();
        return $this->json(['events' => ['events_count' => count($events), $events],]);
    }

    /**
     * Создание пользователя в UTM5 на основании запросов из битрикс
     * @IsGranted(UserAccess::CREATE, subject="request")
     * @Route("/bitrixcreateuser/", name=".add.user", methods={"POST"})
     */
    public function addUTM5User(Request $request,
                                   BitrixRestService $bitrixRestService,
                                   URFAService $URFAService,
                                   LoggerInterface $bitrixLogger): JsonResponse
    {
        if($request->request->has('document_id')) {
            $bitrixLogger->info("Bitrix create request", $request->request->get('document_id'));
            $did = $request->request->get('document_id');
            $login = $did[2]; //DEAL_<NUM>
            [,$dealId] = explode('_', $did[2]); //DEAL_<NUM>
            $data = $bitrixRestService->getDeal($dealId); // данные о сделке ['address', 'id', 'name', 'phone', 'utm5_id', 'status']
            $uid = $URFAService->addUser($login, $data['phone'], $data['address'], $data['name']);
            $bitrixRestService->updateDeal($dealId, [BitrixRestService::DEAL_UTM5_ID_FIELD => $uid,]);
            $bitrixLogger->info('User created', ['uid' => $uid, $data]);
            return $this->json(["success" =>  'deal updated']);
        } else {
            $bitrixLogger->error('Переменная document_id не задана');
            return $this->json(['error' => 'document_id not found']);
        }
    }

    /**
     * Удаление пользователя в биллинге если клиент отказался
     * @param Request $request
     * @param BitrixRestService $bitrixRestService
     * @param URFAService $URFAService
     * @param LoggerInterface $bitrixLogger
     * @return JsonResponse
     * @IsGranted(UserAccess::REMOVE, subject="request")
     * @Route("/bitrixremoveuser/", name=".remove.user", methods={"POST"})
     */
    public function  deleteUTM5User(Request $request,
                                    BitrixRestService $bitrixRestService,
                                    URFAService $URFAService,
                                    LoggerInterface $bitrixLogger): JsonResponse
    {
        if($request->request->has('document_id')) {
            $did = $request->request->get('document_id');
            [,$dealId] = explode('_', $did[2]); //DEAL <NUM>
            $data = $bitrixRestService->getDeal($dealId); // данные о сделке ['address', 'id', 'name', 'phone', 'utm5_id', 'status']
            $bitrixLogger->info("Remove user with deal", $data);
            if(!empty($data['utm5_id'])) {
                $uid = $URFAService->removeUser($data['utm5_id']);
                $bitrixRestService->updateDeal($dealId, [BitrixRestService::DEAL_UTM5_ID_FIELD => 0,]);
                $bitrixLogger->info("User {$data['utm5_id']} удален ", $uid);
            }
            return $this->json(['success' => 'user removed']);
        } else {
            $bitrixLogger->error('Переменная document_id не задана при удалении');
            return $this->json(['error' => 'document_id not found']);
        }
    }

    /**
     * @param Request $request
     * @param LoggerInterface $bitrixLogger
     * @param Paycheck\Handler $handler
     * @return JsonResponse
     * IsGranted(UserAccess::PAYCHECK, subject="request")
     * @Route("/paycheck/", name=".pay-check.user", methods={"POST"})
     */
    public function checkUTM5Payments(Request $request,
                                      LoggerInterface $bitrixLogger,
                                      Paycheck\Handler $handler): JsonResponse
    {
        try {
            $command = new Paycheck\Command($request->request->get('document_id', []));
            $bitrixLogger->info("Check payments for deal", $request->request->get('document_id', []));
            if ($handler->handle($command)) {
                $bitrixLogger->info("Deal updated");
                return $this->json(['result' => 'success']);
            } else {
                $bitrixLogger->info("Deal not updated");
                return $this->json(['result' => 'error']);
            }
        } catch (InvalidArgumentException | DomainException $e) {
            $bitrixLogger->error($e->getMessage());
            return $this->json(['result' => 'error']);
        }
    }
}
