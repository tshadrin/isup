<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order\Order;
use App\Entity\UTM5\UTM5User;
use App\Repository\Order\OrderRepository;
use cebe\markdown\MarkdownExtra;
use App\Form\{ Order\OrderForm, Rows, RowsForm };
use App\ReadModel\Orders\ShowList\{ Filter, OrdersFetcher };
use App\Repository\UTM5\PassportRepository;
use App\Service\{Order\OrderService,
    Order\ShowList,
    UTM5\UTM5DbService};
use App\Service\Order\Edit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{ IsGranted, ParamConverter };
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ JsonResponse, RedirectResponse, Response, Request, Session\Session };
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/order", name="order")
 * @IsGranted("ROLE_SUPPORT")
 */
class OrderController extends AbstractController
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_ROWS_ON_PAGE = 30;

    /** @var OrderRepository  */
    private $orderRepository;
    /** @var OrderService  */
    private $orderService;
    /** @var TranslatorInterface  */
    private $translator;

    public function __construct(OrderRepository $orderRepository,
                                OrderService $orderService,
                                TranslatorInterface $translator)
    {
        $this->orderRepository = $orderRepository;
        $this->translator = $translator;
        $this->orderService = $orderService;
    }

    /**
     * @Route("", name="", methods={"GET"})
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
     * @IsGranted("ROLE_ORDER_MODERATOR")
     * @Route("/{order_id}/delete", name=".delete", methods={"POST"}, requirements={"order_id": "\d+"})
     * @ParamConverter("order", options={"id" = "order_id"})
     */
    public function delete(Request $request, Order $order): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('order');
        }

        try {
            $order->delete($this->getUser());
            $this->orderRepository->save($order);
            $this->orderRepository->flush();

            $this->addFlash('notice', $this->translator->trans('Order %id% deleted', ["%id%" => $order->getId()]));
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans(
                "Error deleting order: %error%",
                ["%error%" => $this->translator->trans($e->getMessage())]
            ));
        }
        return $this->redirectToRoute("order");
    }

    /**
     * @IsGranted("ROLE_ORDER_MODERATOR")
     * @Route("/{order_id}/delete/ajax", name=".delete.ajax", methods={"POST"}, requirements={"order_id": "\d+"})
     * @ParamConverter("order", options={"id" = "order_id"})
     */
    public function deleteAjax(Request $request, Order $order): JsonResponse
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->json(['result' => "error", "message" => "Invalid csrf token"]);
        }

        try {
            $order->delete($this->getUser());
            $this->orderRepository->save($order);
            $this->orderRepository->flush();

            return $this->json([
                'result' => 'success',
                'message' => $this->translator->trans('Order %id% deleted', ["%id%" => $order->getId(),]),
            ]);
        } catch (\DomainException $e) {
            return $this->json([
                'result' => 'error',
                'message' =>  $this->translator->trans("Error deleting order: %error%", ["%error%" => $this->translator->trans($e->getMessage()),]),
            ]);
        }

    }

    /**
     * @return RedirectResponse|Response
     * @Route("/add", name=".add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response
    {
        $order = $this->orderRepository->getNew();
        $form = $this->createForm(OrderForm::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order =$form->getData();
            $this->orderRepository->save($order);
            $this->orderRepository->flush();

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
     * @Route("/{utm5_user_id}/add", name=".add_from_user", methods={"GET", "POST"}, requirements={"utm5_user_id": "\d+"})
     * @ParamConverter("UTM5User", options={"id" = "utm5_user_id"})
     */
    public function addFromUser(UTM5User $UTM5User, Request $request, UTM5DbService $UTM5DbService): Response
    {
        $order = $this->orderService->createByUTM5User($UTM5User, $request->query->get('comment', ''));

        $form = $this->createForm(OrderForm::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();
            $this->orderRepository->save($order);
            $this->orderRepository->flush();

            $this->addFlash('notice', 'order.order_created');
            if ($form['saveandlist']->isCLicked())
                return $this->redirectToRoute('order');
            if ($form['saveandback']->isCLicked())
                return $this->redirectToRoute('search.by.data',
                    ['type' => 'id', 'value' => $UTM5User->getId(),]);
        }
        return $this->render('Order/order_form.html.twig', ['form' => $form->createView(),]);
    }

    /**
     * @return RedirectResponse|Response
     * @Route("/{order_id}/print", name=".print", methods={"GET"}, requirements={"order_id": "\d+"})
     * @ParamConverter("order", options={"id" = "order_id"})
     */
    public function print(Order $order, OrderService $orderService, PassportRepository $passportRepository): Response
    {
        try {
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
    public function changeEditableField(Request $request, OrderService $orderService, MarkdownExtra $markdownExtra): Response
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
     * @Route("", name=".rows", methods={"POST"})
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

    /**
     * @Route("/{order}/edit/status", name=".edit.status", methods={"POST"})
     */
    public function editStatus(Order $order, Request $request, Edit\Status\Handler $handler, TranslatorInterface $translator): JsonResponse
    {
        if (!$this->isCsrfTokenValid('edit', $request->request->get('token'))) {
            return $this->json(["result" => "error", "message" => $translator->trans("Invalid token")]);
        }

        try {
            $command = new Edit\Status\Command($order, $request->request->getInt('value'));
            $handler->handle($command);
        } catch (\InvalidArgumentException | \DomainException $e) {
            return $this->json(["result" => "error", "message" => $e->getMessage()]);
        }

        return $this->json(['id' => "#status-{$order->getId()}",
            'value' => $order->getStatus()->getDescription(),
            'message' => $translator->trans("Order Status updated")]);
    }

    /**
     * @Route("/{order}/edit/comment", name=".edit.comment", methods={"POST"})
     */
    public function editComment(Order $order,
                                Request $request,
                                Edit\Comment\Handler $handler,
                                TranslatorInterface $translator,
                                MarkdownExtra $markdownExtra): JsonResponse
    {
        if (!$this->isCsrfTokenValid('edit', $request->request->get('token'))) {
            return $this->json(["result" => "error", "message" => $translator->trans("Invalid token")]);
        }

        try {
            $command = new Edit\Comment\Command($order, $request->request->get('value'));
            $handler->handle($command);
        } catch (\InvalidArgumentException | \DomainException $e) {
            return $this->json(["result" => "error", "message" => $e->getMessage()]);
        }

        return $this->json([
            'newValue' => $markdownExtra->parse($request->request->get('value')),
            'message' => $translator->trans("Comment updated"),
            ]);
    }
}
