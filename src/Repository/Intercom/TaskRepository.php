<?php
declare(strict_types=1);

namespace App\Repository\Intercom;

use App\Repository\SaveAndFlush;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Intercom\{ Status, Task, Type };
use App\Entity\User\User;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskRepository extends ServiceEntityRepository
{
    use SaveAndFlush;

    /** @var TranslatorInterface  */
    private $translator;

    public function __construct(ManagerRegistry $registry, TranslatorInterface $translator)
    {
        parent::__construct($registry, Task::class);
        $this->translator = $translator;
    }

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

    public function delete(Task $task): void
    {
        $task->setDeleted(true);
        $this->save($task);
        $this->flush();
    }

    /**
     * @throws \Exception
     */
    public function getNew(User $user): Task
    {
        return new Task($user);
    }
}
