<?php

namespace App\Controller\UTM5;

use Knp\Component\Pager\PaginatorInterface;
use App\Service\BitrixCal\BitirixCalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Collection\UTM5\UTM5UserCollection;
use App\Entity\UTM5\UTM5User;
use App\Event\UTM5UserFoundEvent;
use App\Form\UTM5\UTM5UserCommentForm;
use App\Service\UTM5\UTM5DbService;
use App\Service\UTM5\URFAService;
use App\Service\UTM5\UTM5UserCommentService;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\Annotation\Route;

class UTM5Controller extends AbstractController
{
    /**
     * @param BitirixCalService $bitirix_cal_service
     */
    private function calEvents(BitirixCalService $bitirix_cal_service)
    {
        $result = $bitirix_cal_service->getActualCallEvents();
        $events = $result['events'];
        $events_count = array_shift($events);
        if (null !== $events && $events_count > 0) {
            foreach ($events as $event) {
                $this->addFlash('info', "{$event[0]['title']}: {$event[0]['description']}");
            }
        }
    }

    /**
     * @param UTM5User $user
     */
    private function setChain(UTM5User $user) {
        $queryData = http_build_query(['id' => $user->getId()]);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://bot.istra.news/idsw',
            CURLOPT_POSTFIELDS => $queryData,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_TIMEOUT => 1,
        ]);
        if($result = curl_exec($curl)) {
            $crawler = new Crawler($result);
            $crawler = $crawler->filter('body > div > div > table > tr > td')
                               ->last()
                               ->filter('p')
                               ->first();
            $html = '';
            foreach ($crawler as $domElement) {
                $html .= $domElement->ownerDocument->saveHTML($domElement);
            }
            list($chain) = explode('<br><br><br>', $html);
            $tmp = explode("<br>", $chain);
            array_shift($tmp);
            $chain =  implode("<br>", $tmp);
            $user->setChain($chain);
        }
        curl_close($curl);
    }

    /**
     * Поиск пользователей в базе данных
     * @param $type - тип поискового запроса
     * @param $value - значение для поиска
     * @param Request $request
     * @param BitirixCalService $bitirix_cal_service
     * @param UTM5DbService $UTM5_db_service
     * @param URFAService $URFA_service
     * @param UTM5UserCommentService $UTM5_user_comment_service
     * @param EventDispatcherInterface $event_dispatcher
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("/search/{type}/{value}/", name="search", methods={"GET"}, requirements={"type": "id|fullname|address|ip|login"})
     */
    public function searchAction($type,
                                 $value,
                                 Request $request,
                                 BitirixCalService $bitirix_cal_service,
                                 UTM5DbService $UTM5_db_service,
                                 URFAService $URFA_service,
                                 UTM5UserCommentService $UTM5_user_comment_service,
                                 EventDispatcherInterface $event_dispatcher,
                                 PaginatorInterface $paginator)
    {
        $this->calEvents($bitirix_cal_service);

        try {
            $search_result = $UTM5_db_service->search($value, $type);
            if($search_result instanceof UTM5User) {
                $this->setChain($search_result);
                $template_data = $event_dispatcher->dispatch(
                    new UTM5UserFoundEvent($search_result),
                    UTM5UserFoundEvent::EVENT_NAME
                )->getResult();
                $search_result->setRequirementPayment($URFA_service->getRequirementPaymentForUser($search_result->getAccount()));
                $template_data['user'] = $search_result;
                $comment = $UTM5_user_comment_service->getNewUTM5UserComment($this->getUser());
                $comment->setUtmId($search_result->getId());
                $form = $this->createForm(UTM5UserCommentForm::class, $comment);
                $form->handleRequest($request);
                $template_data['form'] = $form->createView();
                return $this->render('Utm/find.html.twig', $template_data);
            }
            // @todo поправить количество строк на странице
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
                return $this->render('Utm/find.html.twig', ['users' => $paged_users, 'rows' => $rows]);
            }

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->render('Utm/find.html.twig');
    }

    /**
     * Шаблон поиска по-умолчанию
     * @param BitirixCalService $bitirix_cal_service
     * @return Response
     * @Route("/search/", name="search_default", methods={"GET"})
     */
    public function searchDefaultAction(BitirixCalService $bitirix_cal_service)
    {
        $this->calEvents($bitirix_cal_service);
        return $this->render('Utm/find.html.twig');
    }

    /**
     * Метод получает значение из формы и перенаправляет
     * на информацию о нужном пользователе
     * название роута search_post
     * @param Request $request
     * @return RedirectResponse
     * @Route("/search/", name="search_post", methods={"POST"})
     */
    public function searchPostAction(Request $request)
    {
        if ($request->request->has('type') && $request->request->has('value')) {
            $type = $request->request->getAlpha('type');
            $value = $request->request->get('value');
            return $this->redirectToRoute('search',['type' => $type, 'value' => $value,]);
        }
        return $this->redirectToRoute("search_default");
    }

    /**
     * Удаление комментария
     * @param $id
     * @param UTM5UserCommentService $UTM5_user_comment_service
     * @return RedirectResponse
     * @Route("/utm5-user-comment/{id}/delete/", name="utm5_user_comment_delete", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function UTM5UserCommentDeleteAction($id, UTM5UserCommentService $UTM5_user_comment_service)
    {

        try {
            $id = $UTM5_user_comment_service->delete($id);
            $this->addFlash('notice', 'utm5_user_comment.deleted');
            return $this->redirectToRoute('search', ['type' => 'id', 'value' => $id]);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('search_default');
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
    public function  UTM5UserCommentAddAction(Request $request, UTM5UserCommentService $UTM5_user_comment_service)
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
        return $this->redirectToRoute('search', ['type' => 'id', 'value' => $comment->getUtmId(),]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/search/rop/", name="utm5_rows_on_page", methods={"POST"})
     */
    public function rowsOnPageAction(Request $request)
    {
        $user = $this->getUser();
        if($request->request->has('rows')) {
            $user->setOption('utm5_search_rows', $request->request->get('rows'));
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();
        }
        return $this->json(['refresh' => true]);
    }
}
