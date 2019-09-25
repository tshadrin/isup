<?php
declare(strict_types=1);

namespace App\ReadModel\Orders\ShowList;


use App\Entity\User\User;
use App\ReadModel\Orders\ShowList\Filter\Filter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrdersFetcher
{
    /** @var Connection  */
    private $connection;
    /** @var int */
    private $currentUserId;

    public function __construct(Connection $defaultConnection, TokenStorageInterface $tokenStorage)
    {
        $this->connection = $defaultConnection;
        $user = $tokenStorage->getToken()->getUser();
        $this->currentUserId =
            $user instanceof User ?
                $user->getId() :
                null;
    }

    public function getFilteredOrders(Filter $filter): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select("o.*, c.full_name as created_user_name, s.description as status")
            ->from("orders", 'o')
            ->innerJoin("o", "userrs", "c", "o.user_id = c.id")
            ->join("o", 'statuses', 's', "o.status_id = s.id");

        if (!is_null($filter->text)) {
            $queryBuilder->andWhere("o.full_name LIKE :text")
                ->orWhere("o.address LIKE :text")
                ->orWhere("o.ip_address LIKE :text")
                ->orWhere("o.server_name LIKE :text")
                ->orWhere("o.mobile_telephone LIKE :text")
                ->orWhere("o.comment LIKE :text")
                ->setParameter(":text", "%{$filter->text}%");
        }

        if (!is_null($filter->status)) {
            $queryBuilder->andWhere("s.id = :status")
                ->setParameter(":status", $filter->status->getId());
        }

        if (!is_null($filter->interval)) {
            $queryBuilder->andWhere("DATE(o.created) BETWEEN STR_TO_DATE(:start, '%Y-%m-%d') AND STR_TO_DATE(:end, '%Y-%m-%d')")
                ->setParameter(":start", (new \Datetime)->setTimestamp($filter->interval[0]->getTimestamp())->format("Y-m-d"))
                ->setParameter(":end", (new \Datetime)->setTimestamp($filter->interval[1]->getTimestamp())->format("Y-m-d"));
        }

        if (!is_null($filter->preset)) {
            switch ($filter->preset) {
                case Filter::PRESET_DEDOVSK:
                    $queryBuilder->andWhere("o.server_name IN('Snegiri','Snegiri1','Dedovsk','Dedovsk1','Dedovsk2','Dedovsk3','Sloboda')");
                    break;
                case Filter::PRESET_ISTRA:
                    $queryBuilder->andWhere("o.server_name NOT IN('Snegiri','Snegiri1','Dedovsk','Dedovsk1','Dedovsk2','Dedovsk3','Sloboda')");
                    break;
                case Filter::PRESET_ACTUAL:
                    $queryBuilder->andWhere("DATE(o.created) BETWEEN STR_TO_DATE(:start, '%Y-%m-%d') AND NOW()")
                        ->setParameter(":start", (new \Datetime())->modify("-2 days")->format("Y-m-d"));
                    break;
                case Filter::PRESET_OUTDATE:
                    $queryBuilder->andWhere("DATE(o.created) < STR_TO_DATE(:start, '%Y-%m-%d')")
                        ->setParameter(":start", (new \Datetime())->modify("-2 days")->format("Y-m-d"));
                    break;
                case Filter::PRESET_CURRENT_USER:
                    $queryBuilder->andWhere("o.executed = :user")
                        ->setParameter(":user", $this->currentUserId);
                    break;
                case Filter::PRESET_NOT_ASSIGNED:
                    $queryBuilder->andWhere("o.executed IS NULL");
                    break;
            }
        }

        $result =  $queryBuilder->andWhere('o.is_deleted=0')->orderBy("o.created", "DESC")->execute();

        $rows = $result->fetchAll(FetchMode::CUSTOM_OBJECT, Order::class);
        return $rows;
    }
}