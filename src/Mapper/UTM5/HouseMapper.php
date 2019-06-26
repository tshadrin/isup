<?php

namespace App\Mapper\UTM5;

use App\Entity\UTM5\House;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;
use Symfony\Contracts\Translation\TranslatorInterface;

class HouseMapper
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
    protected function getHouseDataStmt(): Statement
    {
        $sql = "SELECT h.id, h.region, h.city, h.street, h.number, h.ip_zone_id
                FROM houses h
                WHERE h.id=:id";
        return $this->connection->prepare($sql);
    }
    /**
     * Поиск дома по id дома
     * @param int $house_id
     * @return House
     */
    public function getHouse(int $house_id): House
    {
        try {
            $stmt = $this->getHouseDataStmt();
            $stmt->execute([':id' => $house_id]);
            if (1 === $stmt->rowCount()) {
                $data = $stmt->fetch(\PDO::FETCH_ASSOC);
                $house = new House($data['id'], $data['region'], $data['city'], $data['street'], $data['number']);
                return $house;
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("House data query error: %message%", ['%message%' => $e->getMessage()]));
        }
        throw new \DomainException($this->translator->trans("House not found with id %id%", ['%id%' => $house_id]));
    }
}