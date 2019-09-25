<?php
declare(strict_types=1);

namespace App\Controller\UTM5;

use App\Collection\UTM5\UTM5UserCollection;
use App\Kernel;
use App\ReadModel\UTM5\CallsFetcher;
use App\Repository\UTM5\CallRepository;
use App\Repository\UTM5\TypicalCallRepository;
use App\Repository\UTM5\UserFillingInDataRepository;
use phpcent\Client;
use App\Entity\UTM5\{Call, UserFillingInData, UTM5User, Passport};
use App\Event\UTM5UserFoundEvent;
use App\Form\SMS\{ SmsTemplateForm, SmsTemplateData };
use App\Form\UTM5\{PassportForm, PassportFormData, TypicalCallForm, UTM5UserCommentForm};
use App\Service\Bitrix\Calendar\CalendarInterface;
use App\Service\Bot\Chain;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\UTM5\{ URFAService, UTM5DbService, UTM5UserCommentService };
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response, RedirectResponse};
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UTM5Controller
 * @package App\Controller\UTM5
 * @IsGranted("ROLE_SUPPORT")
 */
class UTM5Controller extends AbstractController
{
    /**
     * @var CalendarInterface
     */
    private $calendar;

    public function __construct(CalendarInterface $calendar)
    {
        $this->calendar = $calendar;
    }

    private function calEvents(): void
    {
        $events = $this->calendar->getActualEvents();
        foreach ($events as $event) {
            $this->addFlash('info', "{$event['title']} {$event['description']}");
        }
    }

    /**
     * Поиск пользователей в базе данных
     * @param $type - тип поискового запроса
     * @param $value - значение для поиска
     * @param Request $request
     * @param UTM5DbService $UTM5_db_service
     * @param URFAService $URFA_service
     * @param UTM5UserCommentService $UTM5_user_comment_service
     * @param EventDispatcherInterface $event_dispatcher
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("/search/{type}/{value}", name="search.by.data", methods={"GET"}, requirements={"type": "id|fullname|address|ip|login|phone"})
     */
    public function search(string $type,
                           $value,
                           Request $request,
                           Chain\Parser $parser,
                           UTM5DbService $UTM5_db_service,
                           URFAService $URFA_service,
                           UTM5UserCommentService $UTM5_user_comment_service,
                           EventDispatcherInterface $event_dispatcher,
                           CallsFetcher $callsFetcher,
                           PaginatorInterface $paginator): Response
    {
        $this->calEvents();

        try {
            $search_result = $UTM5_db_service->search($value, $type);
            if($search_result instanceof UTM5User) {
                try {
                    $chain = $parser->getChain($search_result->getId());
                    $search_result->setChain($chain);
                } catch (\DomainException $e) {
                    $this->addFlash('error', $e->getMessage());
                }
                $template_data = $event_dispatcher->dispatch(
                    new UTM5UserFoundEvent($search_result)
                )->getResult();
                try{
                    $search_result->setRequirementPayment($URFA_service->getRequirementPaymentForUser($search_result->getAccount()));
                } catch (\DomainException $e) {
                    $this->addFlash('error', $e->getMessage());
                }
                $template_data['user'] = $search_result;
                $comment = $UTM5_user_comment_service->getNewUTM5UserComment($this->getUser());
                $comment->setUtmId($search_result->getId());
                $form = $this->createForm(UTM5UserCommentForm::class, $comment);
                $form->handleRequest($request);
                $smsTemplateData = new SmsTemplateData();
                $smsTemplateData->setUtmId($search_result->getId());
                $smsTemplateData->setPhone($search_result->getMobilePhone());
                $smsTemplateForm = $this->createForm(SmsTemplateForm::class, $smsTemplateData);
                $smsTemplateForm->handleRequest($request);
                $template_data['smsForm'] = $smsTemplateForm->createView();
                $template_data['form'] = $form->createView();
                $template_data['searchType'] = $type;
                $template_data['calls'] = $callsFetcher->findByUTM5UserId($search_result->getId());
                $form = $this->createForm(TypicalCallForm::class);
                $form->setData(['utm5_id' => $search_result->getId()]);
                $template_data['callform'] = $form->createView();
                return $this->render('Utm/find.html.twig', $template_data);
            }
            if($search_result instanceof UTM5UserCollection) {
                $user = $this->getUser();
                if ($user->hasOption('utm5_search_rows'))
                    $rows = $user->getOption('utm5_search_rows');
                else
                    $rows = 25;
                $paged_users = $paginator->paginate($search_result,
                    $request->query->getInt('page', 1),
                    $rows=='all'?count($search_result):$rows);
                $paged_users->setCustomParameters(['align' => 'center', 'size' => 'small',]);
                return $this->render('Utm/find.html.twig', ['searchType' => $type, 'users' => $paged_users, 'rows' => $rows]);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->render('Utm/find.html.twig');
    }

    /**
     * @return Response
     * @Route("/search/", name="search", methods={"GET"})
     */
    public function searchDefault(): Response
    {
        $this->calEvents();
        $form = $this->createForm(TypicalCallForm::class);
        return $this->render('Utm/find.html.twig', ['callform' => $form->createView()]);
    }

    /**
     * Метод получает значение из формы и перенаправляет
     * на информацию о нужном пользователе
     * название роута search_post
     * @param Request $request
     * @return RedirectResponse
     * @Route("/search/", name="search_post", methods={"POST"})
     */
    public function searchPost(Request $request): RedirectResponse
    {
        if ($request->request->has('type') && $request->request->has('value')) {
            $type = $request->request->getAlpha('type');
            $value = $request->request->get('value');
            return $this->redirectToRoute('search.by.data',['type' => $type, 'value' => $value,]);
        }
        return $this->redirectToRoute("search");
    }

    /**
     * Удаление комментария
     * @param int $id
     * @param UTM5UserCommentService $UTM5_user_comment_service
     * @return RedirectResponse
     * @Route("/utm5-user-comment/{id}/delete/", name="utm5_user_comment_delete", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function UTM5UserCommentDelete(int $id,
                                          UTM5UserCommentService $UTM5_user_comment_service): RedirectResponse
    {
        try {
            $id = $UTM5_user_comment_service->delete($id);
            $this->addFlash('notice', 'utm5_user_comment.deleted');
            return $this->redirectToRoute('search.by.data', ['type' => 'id', 'value' => $id]);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('search');
        }
    }

    /**
     * Обработка формы добавления комментария
     * @param Request $request
     * @param UTM5UserCommentService $UTM5_user_comment_service
     * @return RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/utm5-user-comment/add/", name="utm5_user_comment_add", methods={"POST"})
     */
    public function  UTM5UserCommentAdd(Request $request,
                                        UTM5UserCommentService $UTM5_user_comment_service): RedirectResponse
    {
        $comment = $UTM5_user_comment_service->getNewUTM5UserComment($this->getUser());
        $form = $this->createForm(UTM5UserCommentForm::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $UTM5_user_comment_service->save($comment);
            $this->addFlash('notice', 'utm5_user_comment.created');
        } else {
            $this->addFlash('error', 'utm5_user_comment.not_created');
        }
        return $this->redirectToRoute('search.by.data', ['type' => 'id', 'value' => $comment->getUtmId(),]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param UTM5DbService $UTM5DbService
     * @param URFAService $URFAService
     * @return RedirectResponse|Response
     * @Route("/utm5/passport/{id}/edit/", name="utm5_passport_edit", methods={"GET", "POST"}, requirements={"id": "\d+"})
     */
    public function editPassport(int $id, Request $request,
                                 UTM5DbService $UTM5DbService,
                                 URFAService $URFAService,
                                 UserFillingInDataRepository $userFillingInDataRepository)
    {
        try {
            $user = $UTM5DbService->search((string)$id, 'id');
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('search');
        }

        $form = $this->createForm(PassportForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $passportFormData = $form->getData();
                $passport = Passport::createFromPassportFormData($passportFormData);
                try {
                    $URFAService->editPassport($passport, $user->getId());
                    $this->addFlash("notice", "Data updated");
                    $userFillingInData = new UserFillingInData($this->getUser(),$user->getId());
                    $userFillingInDataRepository->save($userFillingInData);
                    if($form['saveandback']->isClicked()) {
                        return $this->redirectToRoute('search.by.data', ['value' => $user->getId(), 'type' => 'id']);
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                }
            }
        } else {
            if (($passport = $user->getPassportO()) instanceof Passport) {
                $passportFormData = PassportFormData::createFromPassport($passport);
                $passportFormData->setUserId($user->getId());
                $form->setData($passportFormData);
            }
        }
        return $this->render('Utm/edit-passport.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/search/{type}/{value}/", name="search.by.data.rows", methods={"POST"}, requirements={"type": "id|fullname|address|ip|login|phone"})
     */
    public function rowsOnPage(Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->getUser();
        if($request->request->has('rows')) {
            $user->setOption('utm5_search_rows', $request->request->get('rows'));
            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->redirect($request->getUri());
    }

    /**
     * @Route("/search/add-call/ajax", name="search.add-call.ajax", methods={"POST"})
     */
    public function addCallAjax(Request $request, CallRepository $callRepository, TranslatorInterface $translator): JsonResponse
    {
        $form = $this->createForm(TypicalCallForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $call = new Call(new \DateTimeImmutable(), $data['call_type'], (int)$data['utm5_id'], $this->getUser());
            $callRepository->save($call);
            $callRepository->flush();
            return $this->json(["result" => "success", "message" => $translator->trans("Call registered")]);
        }
        return $this->json(["result" => "success", "message" => $translator->trans("Call not registered")]);
    }
    /**
     * @Route("/search/add-call", name="search.add-call", methods={"POST"})
     */
    public function addCall(Request $request): JsonResponse
    {
        $form = $this->createForm(TypicalCallForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->json($data = $form->getData());
        }
        return $this->json(["result" => "success"]);
    }
}
