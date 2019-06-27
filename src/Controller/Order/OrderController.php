<?php

namespace App\Controller\Order;

use App\Entity\UTM5\UTM5User;
use App\Form\Order\OrderFilterType;
use App\Repository\UTM5\UTM5UserRepository;
use App\Service\Order\OrderService;
use App\Form\Order\OrderForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UTM5\UTM5DbService;

class OrderController extends AbstractController
{
    /**
     * @param $id
     * @param OrderService $orderService
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TranslatorInterface $translator
     * @return RedirectResponse
     * @Route("/order/{id}/delete/", name="order_delete", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function deleteAction($id, OrderService $orderService, AuthorizationCheckerInterface $authorizationChecker, TranslatorInterface $translator)
    {
        try {
            if ($authorizationChecker->isGranted('ROLE_ORDER_MODERATOR')) {
                $order_id = $orderService->deleteOrder($id);
                $this->addFlash('notice', $translator->trans('delete_order.success', ["%id%" => $order_id]));
            } else {
                $this->addFlash('notice', 'Недостаточно прав на удаление заявки.');
            }
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute("orders_index");
    }

    /**
     * @param Request $request
     * @param Session $session
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @Route("/order/filter/", name="order_filter", methods={"POST"})
     */
    public function filterAction(Request $request, Session $session, TranslatorInterface $translator)
    {
        if ($request->request->has('filter')) {
            $session->set('filter', $request->request->get('filter'));
            $this->addFlash('notice', $translator->trans('filter_set'));
        } else {
            $this->addFlash('error', $translator->trans('filter_not_set'));
        }
        return $this->json(['refresh' => true]);
    }

    /**
     * Печать заявки
     * @param $id
     * @param OrderService $orderService
     * @return RedirectResponse|Response
     * @Route("/order/{id}/print/", name="order_print", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function printAction($id, OrderService $orderService, UTM5UserRepository $UTM5UserRepository)
    {
        try {
            $order = $orderService->getOrder($id);
            if(!is_null($order->getUtmId())) {
                if(!is_null($order->getUtmId())) {
                    $passport = $UTM5UserRepository->isUserPassportById($order->getUtmId());
                    $order->setEmptyPassport($passport);
                }
            }
            return $this->render('Order/pritable.html.twig', ['order' => $order]);
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('orders_index');
        }
    }

    /**
     * @param OrderService $orderService
     * @param Session $session
     * @return Response
     * @throws \Exception
     * @Route("/orders/", name="orders_index", methods={"GET", "POST"}, options={"expose": true})
     */
    public function indexAction(Request $request, OrderService $orderService, Session $session, UTM5UserRepository $UTM5UserRepository)
    {
        $hideid1 = $session->get('hide_id1', false);
        $hideid2 = $session->get('hide_id2', false);

        $filter = $session->get("filter",'all');

        /*
        $order_filter_form = $this->createForm(OrderFilterType::class);
        $order_filter_form->handleRequest($request);
        if($order_filter_form->isSubmitted())
        {
            dump($order_filter_form->getData());
            exit;
        }*/

        $today_orders = $orderService->findOrdersByFilter($filter);
        foreach($today_orders as $order) {
            if(!is_null($order->getUtmId())) {
                if(!is_null($order->getUtmId())) {
                    $passport = $UTM5UserRepository->isUserPassportById($order->getUtmId());
                    $order->setEmptyPassport($passport);
                }
            }
        }

        $last_orders = $orderService->findOrdersByFilter($filter, false);
        foreach($last_orders as $order) {
            if(!is_null($order->getUtmId())) {
                $passport = $UTM5UserRepository->isUserPassportById($order->getUtmId());
                $order->setEmptyPassport($passport);
            }
        }

        return $this->render('Order/index.html.twig',
            [
                'filter' => $session->get('filter'),
                'today_orders' => $today_orders,
                'orders' => $last_orders,
                'hide_id1' => $hideid1,
                'hide_id2' => $hideid2,
                //'order_filter_form' => $order_filter_form->createView(),
            ]
        );
    }

    /**
     * Скрытие или показ таблиц заявок
     * @param Request $request
     * @param Session $session
     * @return JsonResponse
     * @Route("/order/showhide/", name="order_showhide", methods={"POST"}, options={"expose": true})
     */
    public function showHideAction(Request $request, Session $session)
    {
        if ($request->request->has('hide_id') &&
            $request->request->has('value')
        ) {
            $session->set('hide_id'.$request->request->get('hide_id'), $request->request->getBoolean('value'));
        }
        return $this->json([]);
    }

    /**
     * Действие по умолчанию
     * @param Request $request
     * @param OrderService $orderService
     * @param UTM5DbService $UTM5DbService
     * @param TranslatorInterface $translator
     * @return RedirectResponse
     * @Route("/order/create/", name="order_create", methods={"POST"})
     */
    public function createAction(Request $request,
                                 OrderService $orderService,
                                 UTM5DbService $UTM5DbService,
                                 TranslatorInterface $translator)
    {
        try {
            if ("full" == $request->request->get("create")) {
                $user = $UTM5DbService->search($request->request->getInt("id"));
                $order = $orderService->createOrderByUTM5User($user, $request->request->get('comment', ''));
                $orderService->saveOrder($order);
                $this->addFlash('notice', $translator->trans("order.order_created"));
            }
        } catch (\Exception $e) {
            $this->addFlash('notice', $e->getMessage());
        }
        return $this->redirectToRoute("search", ['type' => 'id', 'value' => $request->request->getInt("id"),]);
    }

    /**
     * @param Request $request
     * @param OrderService $orderService
     * @param UTM5DbService $UTM5DbService
     * @return RedirectResponse|Response
     * @throws \Exception
     * @Route("/order/add/", name="order_add", methods={"GET", "POST"})
     */
    public function addAction(Request $request, OrderService $orderService, UTM5DbService $UTM5DbService)
    {
        if($request->request->has('create') && 'full' == $request->request->has('create')) {
            $utm_user = $UTM5DbService->search($request->request->getInt('id'));
            $order = $orderService->createOrderByUTM5User($utm_user, $request->request->get('comment'));
            $form = $this->createForm(OrderForm::class, $order);
            $form->handleRequest($request);
            return $this->render('Order/order_form.html.twig', ['form' => $form->createView(),]);
        } else {
            $order = $orderService->getNew();
        }
        $form = $this->createForm(OrderForm::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order =$form->getData();
            $orderService->saveOrder($order);
            $this->addFlash('notice', 'order.order_created');
            if ($form['saveandlist']->isCLicked())
                return $this->redirectToRoute('orders_index');
            if ($form['saveandback']->isCLicked())
                return $this->redirectToRoute('search', ['type' => 'id', 'value' => $order->getUtmId(),]);
        }
        return $this->render('Order/order_form.html.twig', ['form' => $form->createView(),]);
    }

    /**
     * Возвращает список статусов для select поля
     * в списке заявок при редактировании
     * @param OrderService $orderService
     * @return JsonResponse
     * @Route("/order/ajax/getstatuses/", name="order_get_statuses", methods={"GET"})
     */
    public function getStatusesAction(OrderService $orderService)
    {
        $statuses = $orderService->getStatusesForFormSelect();
        return $this->json($statuses);
    }

    /**
     * Возвращает список пользователей для select поля
     * доступных для выполнения заявок
     * @param OrderService $orderService
     * @return JsonResponse
     * @Route("/order/ajax/getemployees/", name="order_get_employees", methods={"GET"})
     */
    public function getEmployeesAction(OrderService $orderService)
    {
        $users = $orderService->getUsersForFormSelect();
        return $this->json($users);
    }

    /**
     * Обработка запроса на изменение значения поля заявки
     * В запросе должны содержаться имя поля, новое значение и id заявки
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     * @Route("/order/ajax/change-editable-filed/", name="order_change_editable_field", methods={"POST"})
     */
    public function changeEditableFieldAction(Request $request, OrderService $orderService)
    {
        try {
            if ($request->request->has('name') &&
                $request->request->has('value') &&
                $request->request->has('pk')) {

                $field = $request->request->filter(
                    'name', [],
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/status|comment|executed|is_deleted/',],]
                );
                switch ($field) {
                    case 'status':
                        $order = $orderService->changeOrderStatus(
                            $request->request->getInt('pk'),
                            $request->request->getInt('value')
                        );
                        $data = ['id' => "#status-{$order->getId()}",
                            'value' => $order->getStatus()->getDescription(),
                            'message' => 'Статус заявки изменен.'];
                        break;
                    case 'comment':
                        $orderService->changeOrderComment(
                            $request->request->getInt('pk'),
                            $request->request->get('value')
                        );
                        $data = ['message' => 'Комментарий обновлен.'];
                        break;
                    case 'executed':
                        $orderService->changeOrderExecuted(
                            $request->request->getInt('pk'),
                            $request->request->get('value')
                        );
                        $data = ['message' => 'На выполнение задачи назначен другой сотрудник.'];
                        break;
                }
                return $this->json($data);
            } else {
                $this->addFlash('notice', 'Ошибка при изменении заявки.');
                return $this->redirectToRoute("orders_index");
            }
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            $data['refresh'] = true;
            return $this->json($data);
        }
    }
}
