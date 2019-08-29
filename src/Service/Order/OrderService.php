<?php

namespace App\Service\Order;

use App\Repository\Order\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order\Order;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\UTM5\UTM5User;

class OrderService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var TokenStorageInterface
     */
    private $token_storage;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * OrderService constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param TokenStorageInterface $token_storage
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, TokenStorageInterface $token_storage, OrderRepository $orderRepository)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->token_storage = $token_storage;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Изменение статуса заявки
     * @param $order_id
     * @param $status_id
     * @return mixed
     * @throws \DomainException
     */
    public function changeOrderStatus($order_id, $status_id)
    {
        $order = $this->em->getRepository('App:Order\Order')->findOneById($order_id);
        $status = $this->em->getRepository('App:Intercom\Status')->findOneById($status_id);
        if($order) {
            if ($status) {
                $order->setStatus($status);
                $this->em->persist($order);
                $this->em->flush();
                return $order;
            } else {
                throw new \DomainException($this->translator->trans('status %status_id% not found', ['%status_is%' => $status_id]));
            }
        } else {
            throw new \DomainException($this->translator->trans('order not found with id %id%', ['%id%' => $order_id]));
        }
    }

    /**
     * Изменение комментария заявки.
     * @param $order_id
     * @param $comment
     * @return mixed
     * @throws \DomainException
     */
    public function changeOrderComment($order_id, $comment)
    {
        $order = $this->em->getRepository('App:Order\Order')->findOneById($order_id);
        if($order) {
                $order->setComment($comment);
                $this->em->persist($order);
                $this->em->flush();
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
        $order = $this->em->getRepository('App:Order\Order')->findOneById($order_id);
        if($order) {
            if(0 == $executed_id) {
                $order->deleteExecuted();
                $this->saveOrder($order);
                return $order;
            }
            $executed = $this->em->getRepository('App:User\User')->findOneById($executed_id);
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
    public function getUsersForFormSelect()
    {
        $users = [];
        $u = $this->em->getRepository('App:User\User')->findBy(['onWork' => 1], ['fullName' => 'ASC']);
        foreach ($u as $user) {
            if (!array_key_exists($user->getRegion()->getDescription(), $users))
                $users[$user->getRegion()->getDescription()] = [];
            array_push($users[$user->getRegion()->getDescription()], ['value' => $user->getId(), 'text' => $user->getFullName(),]);
        }
        $users_to_select = [];
        array_push($users_to_select, ['value' => '', 'text' => '',]);
        foreach($users as $region => $u) {
            array_push($users_to_select, ['text' => $region, 'children' => $u]);
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
        $statuses = $this->em->getRepository('App:Intercom\Status')->findAll();
        $statuses_to_select = [];
        foreach($statuses as $status) {
            array_push($statuses_to_select, ['value' => $status->getId(), 'text' => $status->getDescription(),]);
        }
        return $statuses_to_select;
    }

    public function createByUTM5User(UTM5User $user, string $comment)
    {
        $order = Order::createByUTM5User($user);
        $order->setComment($comment);
        $order->setUser($this->token_storage->getToken()->getUser());
        $status = $this->em->getRepository('App:Intercom\Status')->findOneByName('new');
        $order->setStatus($status);
        return $order;
    }

    public function getLastOrders(UTM5User $user)
    {
        return $this->em
            ->getRepository('App:Order\Order')
           ->findBy(['utmId' => $user->getId()],['id'=> 'DESC']);
    }

    public function saveOrder($order)
    {
        $this->em->persist($order);
        $this->em->flush();
    }
}
