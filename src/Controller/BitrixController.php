<?php
declare(strict_types=1);

namespace App\Controller;

<<<<<<< HEAD
use App\Service\Bitrix\BitirixCalService;
use App\Service\Bitrix\User\Command;
use App\Service\Bitrix\User\Handler;
=======
use App\Service\BitrixCal\BitirixCalService;
>>>>>>> origin/master
use App\Service\UTM5\UTM5DbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BitrixController extends AbstractController
{
<<<<<<< HEAD
=======
    const VALID_PHONE_LEN = 11;
>>>>>>> origin/master
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
<<<<<<< HEAD
    public function getUserInfo(Request $request, Handler $handler): JsonResponse
    {
        $command = new Command($request->query->get('phone'));

        try {
            $user = $handler->handle($command);
        } catch (\DomainException | \InvalidArgumentException $e) {
            return $this->json(['result' => 'error', 'message' => $e->getMessage()]);
        }

        return $this->json(['result' => 'success', 'data' => [
            'id' => $user->getId(),
            'full_name' => $user->getFullName(),
            'requirement_payment' => $user->getRequirementPayment(),
            'balance' => $user->getBalance(),
        ],]);
=======
    public function getUserInfo(Request $request, UTM5DbService $UTM5DbService): JsonResponse
    {
        $phone = $request->query->get('phone');
        if(self::isValidPhoneLen($phone)) {
            try {
                $user = $UTM5DbService->search(self::cropPhonePrefix($phone), 'phone');
            } catch (\DomainException $e) {
                return $this->json(['result' => 'error', 'message' => $e->getMessage()]);
            }
            return $this->json(['result' => 'success', 'data' => [
                'id' => $user->getId(),
                'full_name' => $user->getFullName(),
                'requirement_payment' => $user->getRequirementPayment(),
            ],]);
        }
        if ($phone === 1) {
            return $this->json(['result' => 'error', 'message' => 'User not found']);
        }
        return $this->json(['result' => 'error', 'message' => "Invalid phone number length"]);
>>>>>>> origin/master
    }

    /**
     * @Route("/api/bitrix-user-get", name="api.bitrix.user.getinfo", methods={"POST"})
     */
    public function getApp(): Response
    {
        return $this->render('bitrix/get-user.html.twig');
    }
<<<<<<< HEAD
=======

    private static function isValidPhoneLen(string $phone): bool
    {
        return mb_strlen($phone) === self::VALID_PHONE_LEN;
    }
    private static function cropPhonePrefix(string $phone): string
    {
        return mb_substr($phone, 1);
    }

>>>>>>> origin/master
}
