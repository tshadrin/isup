<?php
declare(strict_types=1);

namespace App\Controller;

use App\Security\Voter\Bitrix\UserAccess;

use App\Service\Bitrix\BitrixRestService;
use App\Service\Bitrix\Calendar\CalendarInterface;
use App\Service\Bitrix\User\{ Add, PayCheck, Remove };
use App\Service\Bitrix\Task\Add\Feedback;
use App\Service\Bitrix\Task\Add\ReconciliationReport;
use App\Service\Bitrix\Task\Add\Invoice;
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
            return $this->json(['result' => 'error', 'message' => $e->getMessage()]);
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
            return $this->json(['result' => 'error', $e->getMessage()]);
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

    /**
     * @IsGranted("ROLE_SUPPORT")
     * @Route("/add-invoice-request", name=".add_invoice_request", methods={"POST"})
     */
    public function addInvoiceRequest(Request $request, Invoice\Handler $handler): JsonResponse
    {
        try {
            $command = new Invoice\Command(419, new \DateTimeImmutable());
            $handler->handle($command);
        } catch (\DomainException | \InvalidArgumentException $e) {
            return $this->json(["result" => "error", "message" => $e->getMessage()]);
        }
        return $this->json(['result' => 'success']);
    }
    /**
     * @IsGranted("ROLE_SUPPORT")
     * @Route("/add-reconciliation-report-request", name=".add_reconciliation_report_request", methods={"POST"})
     */
    public function addReconciliationReportRequest(Request $request, ReconciliationReport\Handler $handler): JsonResponse
    {
        try {
            $command = new ReconciliationReport\Command(419, new \DateTimeImmutable());
            $handler->handle($command);
        } catch (\DomainException | \InvalidArgumentException $e) {
            return $this->json(["result" => "error", "message" => $e->getMessage()]);
        }
        return $this->json(['result' => 'success']);
    }

    /**
     * @IsGranted("ROLE_SUPPORT")
     * @Route("/add-feedback-request", name=".add_feedback_request", methods={"POST"})
     */
    public function addFeedbackRequest(Request $request, Feedback\Handler $handler, BitrixRestService $bitrixRestService): JsonResponse
    {
        try {
            $command = new Feedback\Command($request->request->getInt('utm5id'), $request->request->get('comment'));
            $handler->handle($command);
        } catch (\DomainException | \InvalidArgumentException $e) {
            return $this->json(["result" => "error", "message" => $e->getMessage()]);
        }

        return $this->json(['result' => 'success']);
    }
}