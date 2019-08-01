<?php
declare(strict_types=1);

namespace App\Repository\Intercom;

use App\Entity\Intercom\{ Status, Task, Type };
use App\Entity\User\User;
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
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function findAllNotDeleted(): array
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.deleted = 0')
            ->orderBy('p.id', 'ASC')
            ->leftJoin(Status::class, 's', 'with', 's.id = p.status')
            ->leftJoin(Type::class, 't', 'with', 't.id = p.type')
            ->orderBy("p.id", "DESC")
            ->getQuery();
            if(!$tasks = $query->getResult())
                throw new \DomainException($this->translator->trans("Tasks not found"));
        return $tasks;
    }

    /**
     * @param Task $task
     */
    public function delete(Task $task): void
    {
        $task->setDeleted(true);
        $this->save($task);
    }

    /**
     * @param Task $task
     */
    public function save(Task $task): void
    {
        try {
            $this->getEntityManager()->persist($task);
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Task delete error: %error%", ['%error%' => $e->getMessage()]));
        }
        $this->flush();
    }

    public function flush(): void
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
