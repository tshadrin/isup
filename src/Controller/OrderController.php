<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\{ Order\OrderForm, Rows, RowsForm };
use App\ReadModel\Orders\ShowList\{ Filter, OrdersFetcher };
use App\Repository\UTM5\PassportRepository;
use App\Service\{ Order\OrderService, Order\ShowList, UTM5\UTM5DbService };
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ JsonResponse, RedirectResponse, Response, Request, Session\Session };
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @Route("/order", name="order")
 * @IsGranted("ROLE_SUPPORT")
 */
class OrderController extends AbstractController
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_ROWS_ON_PAGE = 30;

    /**
     * @Route("", name="", methods={"GET"}, options={"expose": true})
     */
    public function orders(Request $request, Session $session, ShowList\Handler $handler): Response
    {
        $filter = new Filter\Filter();
        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $rowsOnPage = $session->get('rowsOnPage', self::DEFAULT_ROWS_ON_PAGE);
        $rows = new Rows();
        $rows->value = $rowsOnPage;
        $rowsPerPageForm = $this->createForm(RowsForm::class, $rows);

        $command = new ShowList\Command(
            $filter,
            $request->query->getInt('page',self::DEFAULT_PAGE),
            $rowsOnPage
        );

        try {
            $orders = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render("Order/show-list.html.twig",
            [
                "orders" => $orders,
                "filterForm" => $form->createView(),
                'rowsPerPageForm' => $rowsPerPageForm->createView(),
            ]
        );
    }


    /**
     * @return RedirectResponse|Response
     * @Route("/add", name=".add", methods={"GET", "POST"})
     */
    public function add(Request $request, OrderService $orderService, UTM5DbService $UTM5DbService): Response
    {
        if($request->request->has('create') && 'full' == $request->request->has('create')) {
            $utm_user = $UTM5DbService->search((string)$request->request->getInt('id'));
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
                return $this->redirectToRoute('order');
            if ($form['saveandback']->isCLicked())
                return $this->redirectToRoute('search.by.data',
                    ['type' => 'id', 'value' => $order->getUtmId(),]);
        }
        return $this->render('Order/order_form.html.twig', ['form' => $form->createView(),]);
    }

    /**
     * @return RedirectResponse|Response
     * @Route("/add-from-user", name=".add_from_user", methods={"GET", "POST"})
     */
    public function addFromUser(Request $request, OrderService $orderService, UTM5DbService $UTM5DbService): Response
    {
        if($request->request->has('create') && 'full' == $request->request->has('create')) {
            $utm_user = $UTM5DbService->search((string)$request->request->getInt('id'));
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
                return $this->redirectToRoute('order');
            if ($form['saveandback']->isCLicked())
                return $this->redirectToRoute('search.by.data',
                    ['type' => 'id', 'value' => $order->getUtmId(),]);
        }
        return $this->render('Order/order_form.html.twig', ['form' => $form->createView(),]);
    }

    /**
     * @Route("/{id}/delete", name=".delete", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function delete(int $id, OrderService $orderService,
                           AuthorizationCheckerInterface $authorizationChecker,
                           TranslatorInterface $translator): RedirectResponse
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
        return $this->redirectToRoute("order");
    }

    /**
     * @return RedirectResponse|Response
     * @Route("/{id}/print", name=".print", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function print(int $id, OrderService $orderService, PassportRepository $passportRepository): Response
    {
        try {
            $order = $orderService->getOrder($id);
            if(!is_null($order->getUtmId())) {
                $passport = $passportRepository->getById($order->getUtmId());
                if (!is_null($passport)) {
                    $order->setEmptyPassport($passport->isNotFill());
                } else {
                    $order->setEmptyPassport(true);
                }

            }
            return $this->render('Order/pritable.html.twig', ['order' => $order]);
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('order');
        }
    }

    /**
     * Возвращает список статусов для select поля
     * в списке заявок при редактировании
     * @Route("/ajax/getstatuses", name=".get_statuses", methods={"GET"})
     */
    public function getStatuses(OrderService $orderService): JsonResponse
    {
        $statuses = $orderService->getStatusesForFormSelect();
        return $this->json($statuses);
    }

    /**
     * Возвращает список пользователей для select поля
     * доступных для выполнения заявок
     * @Route("/ajax/getemployees", name=".get_employees", methods={"GET"})
     */
    public function getEmployees(OrderService $orderService): JsonResponse
    {
        $users = $orderService->getUsersForFormSelect();
        return $this->json($users);
    }

    /**
     * Обработка запроса на изменение значения поля заявки
     * В запросе должны содержаться имя поля, новое значение и id заявки
     * @return JsonResponse|RedirectResponse
     * @Route("/ajax/change-editable-filed", name=".change_editable_field", methods={"POST"})
     */
    public function changeEditableField(Request $request, OrderService $orderService): Response
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
                return $this->redirectToRoute("order");
            }
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            $data['refresh'] = true;
            return $this->json($data);
        }
    }

    /**
     * @Route("/order", name=".rows", methods={"POST"})
     */
    public function rowsPerPage(Request $request, Session $session): RedirectResponse
    {
        $rows = new Rows();
        $rowsPerPageForm = $this->createForm(RowsForm::class, $rows);
        $rowsPerPageForm->handleRequest($request);

        if ($rowsPerPageForm->isSubmitted() && $rowsPerPageForm->isValid()) {
            $session->set('rowsOnPage', $rows->value);
        } else {
            $this->addFlash("error", "Incorrect filter value");
        }
        return $this->redirectToRoute("order");
    }
}
