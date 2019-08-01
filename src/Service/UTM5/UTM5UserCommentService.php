<?php

namespace App\Service\UTM5;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\User\User;
use App\Entity\UTM5\UTM5UserComment;

class UTM5UserCommentService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UTM5UserCommentService constructor.
     * @param EntityManager $em
     */
    public  function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @param User $user
     * @return UTM5UserComment
     * @throws \Exception
     */
    public function getNewUTM5UserComment(User $user): UTM5UserComment
    {
        return new UTM5UserComment($user);
    }

    /**
     * @param UTM5UserComment $comment
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(UTM5UserComment $comment)
    {
        $this->em->persist($comment);
        $this->em->flush();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete($id): int
    {
        $comment = $this->em->getRepository("App:UTM5\UTM5UserComment")->findOneById($id);
        if($comment) {
            $id = $comment->getUtmId();
            $this->em->remove($comment);
            $this->em->flush();
            return $id;
        }
        throw new \Exception($this->translator->trans("comment_not_found"));
    }
}
