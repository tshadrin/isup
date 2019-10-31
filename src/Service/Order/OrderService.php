<?php
declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Intercom\Status;
use App\Entity\User\User;
use App\Repository\Intercom\StatusRepostory;
use App\Repository\Order\OrderRepository;
use App\Repository\UserRepository;
use App\Entity\Order\Order;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\UTM5\UTM5User;

class OrderService
{
    /** @var TranslatorInterface */
    private $translator;
    /** @var OrderRepository */
    private $orderRepository;
    /** @var UserRepository */
    private $userRepository;
    /** @var TokenStorageInterface  */
    private $tokenStorage;
    /** @var StatusRepostory */
    private $statusRepostory;

    public function __construct(
        TranslatorInterface $translator,
        TokenStorageInterface $tokenStorage,
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        StatusRepostory $statusRepostory
    )
    {
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->statusRepostory = $statusRepostory;
    }

    /**
     * @return mixed
     * @throws \DomainException
     */
    public function changeOrderStatus(int $orderId, $statusId): Order
    {
        if (($order = $this->orderRepository->findOneById($orderId)) instanceof Order) {
            if (($status = $this->statusRepostory->findOneById($statusId)) instanceof Status) {
                $order->setStatus($status);
                $this->orderRepository->save($order);
                $this->orderRepository->flush();
                return $order;
            } else {
                throw new \DomainException($this->translator->trans('Status %status_id% not found', ['%status_id%' => $statusId]));
            }
        } else {
            throw new \DomainException($this->translator->trans('Order not found with id %id%', ['%id%' => $orderId]));
        }
    }

    /**
     * Изменение комментария заявки.
     * @return mixed
     * @throws \DomainException
     */
    public function changeOrderComment($order_id, $comment)
    {
        /** @var Order $order */
        $order = $this->orderRepository->findOneById($order_id);
        if ($order) {
            $order->setComment($comment);
            $this->orderRepository->save($order);
            $this->orderRepository->flush();
            return $order;
        } else {
            throw new \DomainException($this->translator->trans('order not found with id %id%', ['%id%' => $order_id]));
        }
    }

    /**
     * Назначить сотрудника на выполнение заявки
     * @param $order_id
     * @param $executed_id
     * @return mixed
     * @throws \DomainException
     */
    public function changeOrderExecuted($order_id, $executed_id)
    {
        $order = $this->orderRepository->findOneById($order_id);
        if ($order) {
            if (0 === $executed_id) {
                $order->deleteExecuted();
                $this->saveOrder($order);
                return $order;
            }
            $executed = $this->userRepository->findOneById($executed_id);
            if ($executed) {
                $order->setExecuted($executed);
                $this->saveOrder($order);
                return $order;
            } else {
                throw new \DomainException($this->translator->trans('user %executed_id% not found', ['%executed_id%' => $executed_id]));
            }
        } else {
            throw new \DomainException($this->translator->trans('order not found with id %id%', ['%id%' => $order_id]));
        }
    }

    /**
     * Подготовка данных для заполнения пользователей
     * поля select в списке заявок
     * @return array
     */
    public function getUsersForFormSelect(): array
    {
        $users = [];
        $workers = $this->userRepository->findBy(['onWork' => 1], ['fullName' => 'ASC']);
        foreach ($workers as $worker) {
            if (!array_key_exists($worker->getRegion()->getDescription(), $users))
                $users[$worker->getRegion()->getDescription()] = [];
            array_push($users[$worker->getRegion()->getDescription()], ['value' => $worker->getId(), 'text' => $worker->getFullName(),]);
        }
        $users_to_select = [];
        array_push($users_to_select, ['value' => '', 'text' => '',]);
        foreach($users as $region => $workers) {
            array_push($users_to_select, ['text' => $region, 'children' => $workers]);
        }
        return $users_to_select;
    }

    /**
     * Подготовка данных для заполнения статусов
     * поля select в списке заявок
     * @return array
     */
    public function getStatusesForFormSelect()
    {
        $statuses = $this->statusRepostory->findAll();
        $statuses_to_select = [];
        foreach($statuses as $status) {
            array_push($statuses_to_select, ['value' => $status->getId(), 'text' => $status->getDescription(),]);
        }
        return $statuses_to_select;
    }

    public function createByUTM5User(UTM5User $user, string $comment)
    {
        if (!$this->tokenStorage->getToken()->getUser() instanceof User) {
            throw new \DomainException("User is not logged in");
        }
        $order = Order::createByUTM5User($user);
        $order->setComment($comment);
        $order->setUser($this->tokenStorage->getToken()->getUser());
        $status = $this->statusRepostory->findOneByName('new');
        $order->setStatus($status);
        return $order;
    }

    public function getLastOrders(UTM5User $user)
    {
        return $this->orderRepository->findBy(
            ['utmId' => $user->getId()],
            ['id'=> 'DESC']
        );
    }

    public function saveOrder(Order $order)
    {
        $this->orderRepository->save($order);
        $this->orderRepository->flush();
    }
}
