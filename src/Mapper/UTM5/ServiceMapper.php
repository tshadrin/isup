<?php


namespace App\Mapper\UTM5;

use App\Collection\UTM5\ServiceCollection;
use App\Entity\UTM5\Service;
use Doctrine\DBAL\{ Connection, DBALException };
use Doctrine\DBAL\Driver\Statement;
use Symfony\Contracts\Translation\TranslatorInterface;

class ServiceMapper
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
    public function getServicesDataByAccountStmt(): Statement
    {
        $sql = "SELECT sd.service_name as service_name, psd.cost as cost
                FROM service_links sl
                    INNER JOIN services_data sd
                        ON sd.id=sl.service_id
                    INNER JOIN periodic_services_data psd
                        ON psd.id=sl.service_id
                WHERE sl.is_deleted=0
                  AND sl.tariff_link_id=0
                  AND sl.account_id = :basic_account";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getServicesDataByTariffLinkStmt(): Statement
    {
        $sql = "SELECT sd.service_name as service_name, psd.cost as cost
                FROM service_links sl
                    INNER JOIN services_data sd
                        ON sd.id=sl.service_id
                    INNER JOIN periodic_services_data psd
                        ON psd.id=sl.service_id
                WHERE sl.is_deleted=0
                  AND sl.tariff_link_id = :tariff_link";
        return $this->connection->prepare($sql);
    }

    /**
     * Услуги по аккаунту
     * @param $account
     * @return ServiceCollection|null
     */
    public function getServices(int $account): ?ServiceCollection
    {
        try {
            $stmt = $this->getServicesDataByAccountStmt();
            $stmt->execute([':basic_account' => $account]);
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $services = new ServiceCollection();
                foreach ($data as $row) {
                    $services->add(new Service($row['service_name'], $row['cost']));
                }
                return $services;
            }
            return null;
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Services data query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }

    /**
     * Услуги для тарифной связки
     * @param int $tariff_link
     * @return ServiceCollection|null
     */
    public function getTariffServices(int $tariff_link): ?ServiceCollection
    {
        try {
            $stmt = $this->getServicesDataByTariffLinkStmt();
            $stmt->execute([':tariff_link' => $tariff_link]);
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $services = new ServiceCollection();
                foreach ($data as $row) {
                    $services->add(new Service($row['service_name'], $row['cost']));
                }
                return $services;
            }
            return null;
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Tariff services query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }
}