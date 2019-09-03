<?php
declare(strict_types=1);

namespace App\Controller\UTM5;

use App\Service\UTM5\CallRegister\Command;
use App\Service\UTM5\CallRegister\Handler;
use App\Service\UTM5\URFAService;
use Exception;
use phpcent\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * Меняет статус Интернета пользователя
     * и перенаправляет на его профиль
     * @param int $id  - id клиента
     * @param URFAService $URFA_service
     * @return JsonResponse
     * @IsGranted("ROLE_SUPPORT")
     * @Route("/urfa/change-remindme/{id}/", name="utm_change_remindme", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function changeRemindMe(int $id, URFAService $URFA_service): JsonResponse
    {
        try{
            $URFA_service->changeRemindMe($id);
            return $this->json(['result' => 'success']);
        } catch(Exception $e)
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
     * @IsGranted("ROLE_SUPPORT")
     * @Route("/urfa/change-intstatus/{id}/", name="utm_change_intstatus", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function changeStatus(int $id, URFAService $URFA_service): JsonResponse
    {
        try {
            if($URFA_service->changeInternetStatus($id))
                return $this->json(['result' => 'success']);
            else
                return $this->json(['result' => 'error']);
        } catch(Exception $e) {
            return $this->json(['result' => 'error']);
        }
    }

    /**
     * Обработка запроса на изменение значения полей в карточке клиента
     * @param Request $request
     * @param URFAService $URFAService
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @IsGranted("ROLE_SUPPORT")
     * @Route("/urfa/change-editable-filed/", name="user_change_editable_field", methods={"GET","POST"})
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
                    ['options' => ['regexp' => '/mobile_phone|email|additional_phone/',],]
                );
                switch ($field) {
                    case 'mobile_phone':
                        $phone_number =  mb_ereg_replace('\D+', '', $request->request->get('value'));
                        $phone_number_len = mb_strlen($phone_number);
                        if (($phone_number_len < 10 && $phone_number_len > 0) || $phone_number_len > 11)
                            return $this->json(['result' => 'error', 'message' => $translator->trans('Phone number must contains 10 digits'),]);
                        if (11 === $phone_number_len) {
                            if (mb_ereg_match('8[0-9]', $phone_number)) {
                                $phone_number = mb_substr($phone_number, 1);
                                $URFAService->editMobilePhone(
                                    $phone_number,
                                    $request->request->getInt('pk')
                                );
                                return $this->json(['result' => 'success', 'message' => $translator->trans('Number edit success'),]);
                            } else {
                                return $this->json(['result' => 'error', 'message' => $translator->trans('Incorrect phone number'),]);
                            }
                        }
                        if (10 === $phone_number_len) {
                            if (mb_ereg_match('[0-7,9]', $phone_number)) {
                                $URFAService->editMobilePhone(
                                    $phone_number,
                                    $request->request->getInt('pk')
                                );
                                return $this->json(['result' => 'success', 'message' => $translator->trans('Number edit success'),]);
                            } else {
                                return $this->json(['result' => 'error', 'message' => $translator->trans('Incorrect phone number'),]);
                            }
                        }
                        if (0 === $phone_number_len) {
                            $URFAService->editMobilePhone(
                                $request->request->get('value'),
                                $request->request->getInt('pk')
                            );
                            return $this->json(['result' => 'success', 'message' => $translator->trans('Phone number is clear')]);
                        }
                        break;
                    case 'email':
                        $email = $request->request->filter(
                            'value', [],
                            FILTER_VALIDATE_EMAIL
                        );
                        if (false !== $email) {
                            $URFAService->editEmail(
                                $email,
                                $request->request->getInt('pk')
                            );
                            return $this->json(['result' => 'success', 'message' => $translator->trans('Email updated')]);
                        } else {
                            return $this->json(['result' => 'error', 'message' => $translator->trans("Incorrect email")]);
                        }
                        break;
                    case 'additional_phone':
                        $additionalPhone =  mb_ereg_replace('\D+', '', $request->request->get('value'));
                        $additionalPhoneLength = mb_strlen($additionalPhone);
                        if (false !== $additionalPhone) {
                            $URFAService->editAdditionalPhone(
                                $additionalPhone,
                                $request->request->getInt('pk')
                            );
                            return $this->json(['result' => 'success', 'message' => $translator->trans('Email updated')]);
                        } else {
                            return $this->json(['result' => 'error', 'message' => $translator->trans("Incorrect email")]);
                        }
                        break;
                }
            } else {
                return $this->json(['result' => 'error', 'message' => $translator->trans('Request data not found'),]);
            }
        } catch (Exception $e) {
            return $this->json(['result' => 'error', 'message' => $e->getMessage(),]);
        }
    }

    /**
     * @Route("/api/register-call/{operator_num}/{callerid_num}", name="utm5.register_call", methods={"GET"})
     */
    public function registerCall(int $operator_num,
                                 string $callerid_num,
                                 Handler $handler): JsonResponse
    {
        $command = new Command($operator_num, $callerid_num);
        $handler->handle($command);
        return $this->json(["result" => "success"]);
    }
}
