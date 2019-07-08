<?php

namespace App\Mapper\UTM5;

use App\Collection\UTM5\GroupCollection;
use App\Entity\UTM5\Group;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;
use Symfony\Contracts\Translation\TranslatorInterface;

class GroupMapper
{

    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(Connection $connection, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->translator = $translator;
    }


    /**
     * @return Statement
     * @throws DBALException
     */
    protected function getGroupsDataByIdStmt(): Statement
    {
        $sql = "SELECT g.id, g.group_name AS name
                FROM groups g
                    INNER JOIN users_groups_link u
                        ON u.group_id=g.id
                WHERE u.user_id = :id";
        return $this->connection->prepare($sql);
    }

    /**
     * Поиск групп пользователя. Группы могут быть не назначены
     * @param int $user_id
     * @return GroupCollection|null
     */
    public function getGroups(int $user_id): ?GroupCollection
    {
        try {
            $stmt = $this->getGroupsDataByIdStmt();
            $stmt->execute([':id' => $user_id]);
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $groups = new GroupCollection();
                foreach ($data as $item) {
                    $groups->add(new Group($item['id'], $item['name']));
                }
                return $groups;
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("User groups search error: %message%", ['%message%' => $e->getMessage()]));
        }
        return null;
    }
}