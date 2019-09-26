<?php
declare(strict_types=1);

namespace App\Controller;

use App\Security\Voter\Bitrix\UserAccess;

use App\Service\Bitrix\Calendar\CalendarInterface;
use App\Service\Bitrix\User\{ Add, PayCheck, Remove };
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ JsonResponse, Request };
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="bitrix")
 */
class BitrixController extends AbstractController
{
    /**
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
    public function addUTM5User(Request $request, Add\Handler $handler, LoggerInterface $bitrixLogger): JsonResponse
    {
        try {
            $command = new Add\Command($request->request->get('document_id', []));
            $bitrixLogger->info("Bitrix create request", [$command->document]);
            $handler->handle($command);
            return $this->json(["result" => "success"]);
        } catch (\InvalidArgumentException | \DomainException $e) {
            $bitrixLogger->error($e->getMessage());
            return $this->json(['result' => 'error']);
        }
    }

    /**
     * @IsGranted(UserAccess::REMOVE, subject="request")
     * @Route("/bitrixremoveuser/", name=".remove.user", methods={"POST"})
     */
    public function deleteUTM5User(Request $request, Remove\Handler $handler, LoggerInterface $bitrixLogger): JsonResponse
    {
        try {
            $command = new Remove\Command($request->request->get('document_id', []));
            $bitrixLogger->info("Remove user with deal", [$command->document]);
            $handler->handle($command);
            return $this->json(["result" => "success"]);
        } catch (\InvalidArgumentException | \DomainException $e) {
            $bitrixLogger->error($e->getMessage());
            return $this->json(['result' => 'error']);
        }
    }

    /**
     * @IsGranted(UserAccess::PAYCHECK, subject="request")
     * @Route("/paycheck/", name=".pay-check.user", methods={"POST"})
     */
    public function checkUTM5Payments(Request $request, LoggerInterface $bitrixLogger, PayCheck\Handler $handler): JsonResponse
    {
        try {
            $command = new PayCheck\Command($request->request->get('document_id', []));
            $bitrixLogger->info("Check payments for deal", [$command->document]);
            $handler->handle($command);
            return $this->json(["result" => "success"]);
        } catch (\InvalidArgumentException | \DomainException $e) {
            $bitrixLogger->error($e->getMessage());
            return $this->json(['result' => 'error']);
        }
    }
}