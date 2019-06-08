<?php

namespace App\Repository\Intercom;

use App\Entity\Intercom\Status;
use App\Entity\Intercom\Task;
use App\Entity\Intercom\Type;
use App\Entity\User\User;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\EntityRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TaskRepository
 * @package App\Repository\Intercom
 * @method Task find (integer $id)
 */
class TaskRepository extends EntityRepository
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllNotDeleted()
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.deleted = 0')
            ->orderBy('p.id', 'ASC')
            ->leftJoin(Status::class, 's', 'with', 's.id = p.status')
            ->leftJoin(Type::class, 't', 'with', 't.id = p.type')
            ->orderBy("p.id", "DESC")
            ->getQuery();
        try {
            if(!$tasks = $query->getResult())
                throw new \DomainException($this->translator->trans("Tasks not found"));
        } catch (QueryException $e) {
            throw new \DomainException($this->translator->trans("Task query error: %error%", ['%error%' => $e->getMessage()]));
        }
        return $tasks;
    }

    /**
     * @param Task $task
     */
    public function delete(Task $task)
    {
        $task->setDeleted(true);
        $this->save($task);
    }

    /**
     * @param Task $task
     */
    public function save(Task $task)
    {
        try {
            $this->getEntityManager()->persist($task);
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Task delete error: %error%", ['%error%' => $e->getMessage()]));
        }
        $this->flush();
    }

    public function flush()
    {
        try {
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Task flush error: %error%", ['%error%' => $e->getMessage()]));
        }
    }

    /**
     * @param User $user
     * @return Task
     * @throws \Exception
     */
    public function getNew(User $user): Task
    {
        return new Task($user);
    }
}
