<?php
declare(strict_types=1);

namespace App\Service\Intercom;

use App\Entity\Intercom\Task;
use App\Repository\Intercom\TaskRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


class IntercomService
{
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var PaginatorInterface
     */
    protected $paginator;
    /**
     * @var TaskRepository
     */
    private $taskRepository;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * IntercomService constructor.
     * @param TranslatorInterface $translator
     * @param PaginatorInterface $paginator
     * @param TaskRepository $taskRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TranslatorInterface $translator, PaginatorInterface $paginator, TaskRepository $taskRepository, TokenStorageInterface $tokenStorage)
    {
        $this->translator = $translator;
        $this->paginator = $paginator;
        $this->taskRepository = $taskRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Выборка всех активных задач постранично
     * @param int $page
     * @param int $rows
     * @return PaginationInterface
     */
    public function getAllTasksPaginate(int $page, int $rows=30): PaginationInterface
    {
        $tasks = $this->taskRepository->findAllNotDeleted();
        $paged_tasks = $this->paginator->paginate($tasks, $page, $rows);
        $paged_tasks->setCustomParameters(['align' => 'center', 'size' => 'small',]);
        return $paged_tasks;
    }

    /**
     * Удаление задачи
     * @param int $id
     */
    public function deleteTask(int $id)
    {
        $task = $this->getOneTaskById($id);
        $this->taskRepository->delete($task);
    }

    /**
     * Сохранение задачи
     * @param Task $task
     */
    public function saveTask(Task $task)
    {
        $this->taskRepository->save($task);
    }

    /**
     * Получение задачи по id
     * @param int $id
     * @return Task
     */
    public function getOneTaskById(int $id): Task
    {
        try {
            $task = $this->taskRepository->find($id);
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Task getting error: %error%", ['%error%', $e->getMessage()]));
        }
        if(!$task)
            throw new \DomainException($this->translator->trans('Task not found'));
        return $task;
    }

    /**
     * @return Task
     * @throws \Exception
     */
    public function getNewTask(): Task
    {
        $user = $this->tokenStorage->getToken()->getUser();
        return $this->taskRepository->getNew($user);
    }
}
