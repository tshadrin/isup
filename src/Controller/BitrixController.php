<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\Bitrix\BitirixCalService;
use App\Service\Bitrix\User\Command;
use App\Service\Bitrix\User\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BitrixController extends AbstractController
{
    /**
     * @return JsonResponse
     * @Route("/api/getbitrixcal", name="api.get.bitrix.calendar", methods={"GET"})
     */
    public function getCalendar(BitirixCalService $bitirixCalendarService): JsonResponse
    {
        return $this->json($bitirixCalendarService->getActualCallEvents());
    }

    /**
     * @return JsonResponse
     * @Route("/api/bitrix-user-get", name="api.bitrix.user.get", methods={"GET"})
     */
    public function getUserInfo(Request $request, Handler $handler): JsonResponse
    {
        try {
            $command = new Command($request->query->get('phone'));

            $userFields = $handler->handle($command);
        } catch (\DomainException | \InvalidArgumentException $e) {
            return $this->json(['result' => 'error', 'message' => $e->getMessage()]);
        }

        $response  = $this->json(['result' => 'success', 'data' => $userFields,]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Methods', 'GET');
        $response->headers->set('Access-Control-Allow-Headers',
            'Content-Type, Depth, User-Agent, X-File- Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control');
        return $response;
    }
}
