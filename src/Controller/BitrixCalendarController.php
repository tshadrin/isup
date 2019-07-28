<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\BitrixCal\BitirixCalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BitrixCalendarController extends AbstractController
{
    /**
     * @return JsonResponse
     * @Route("/api/getbitrixcal", name="api.get.bitrix.calendar", methods={"GET"})
     */
    public function getBitrixCalendar(BitirixCalService $bitirixCalendarService): JsonResponse
    {
        return $this->json($bitirixCalendarService->getActualCallEvents());
    }
}
