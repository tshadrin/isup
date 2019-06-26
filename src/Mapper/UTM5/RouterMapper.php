<?php

namespace App\Mapper\UTM5;

use App\Collection\UTM5\RouterCollection;
use App\Entity\UTM5\Router;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;
use Symfony\Contracts\Translation\TranslatorInterface;

class RouterMapper
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
    protected function getRoutersDataByIdStmt(): Statement
    {
        $sql = "SELECT DISTINCT ri.router_comments as name, ri.router_ip as ip
                  FROM users_groups_link ugl
                      INNER JOIN firewall_rules_new f
                          ON ugl.group_id = f.group_id
                      INNER JOIN routers_info ri
                          ON f.router_id = ri.id
                  WHERE ugl.user_id=:id";
        return $this->connection->prepare($sql);
    }

    /**
     * Поиск роутеров по id пользователя
     * @param int $user_id
     * @return RouterCollection|null
     */
    public function getRouters(int $user_id): ?RouterCollection
    {
        try {
            $stmt = $this->getRoutersDataByIdStmt();
            $stmt->execute([':id' => $user_id]);
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $routers = new RouterCollection();
                foreach ($data as $row) {
                    $routers->add(new Router($row['name'], $row['ip']));
                }
                return $routers;
            }
            return null;
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Routers data query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }
}