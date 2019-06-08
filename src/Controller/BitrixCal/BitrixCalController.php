<?php

namespace App\Controller\BitrixCal;

use App\Service\BitrixCal\BitirixCalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BitrixCalController extends AbstractController
{
    /**
     * Выдача событий календаря
     * @return JsonResponse
     * @Route("/api/getbitrixcal", name="get_bitrix_cal", methods={"GET"})
     */
    public function indexAction(BitirixCalService $bitirixCalService): JsonResponse
    {
        return $this->json($bitirixCalService->getActualCallEvents());
    }
}
