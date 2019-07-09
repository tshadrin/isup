<?php


namespace App\Mapper\UTM5;

use App\Collection\UTM5\{ ServiceCollection, TariffCollection };
use App\Entity\UTM5\{ DiscountPeriod, Tariff };
use App\Repository\UTM5\ServiceRepository;
use Doctrine\DBAL\{ Connection, DBALException };
use Doctrine\DBAL\Driver\Statement;
use Symfony\Contracts\Translation\TranslatorInterface;


class TariffMapper
{

    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var ServiceRepository
     */
    private $serviceRepository;

    public function __construct(Connection $connection, TranslatorInterface $translator, ServiceRepository $serviceRepository)
    {
        $this->connection = $connection;
        $this->translator = $translator;
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    protected function getTariffDataByAccountStmt(): Statement
    {
        $sql = "SELECT a.id as tariff_link,
                       c.name AS actual_tariff,
                       n.name AS next_tariff,
                       d.start_date AS discount_period_start,
                       d.end_date AS discount_period_end,
                       d.id AS discount_period
                FROM account_tariff_link a
                    JOIN discount_periods d
                        ON a.discount_period_id=d.id
                    JOIN tariffs c
                        ON a.tariff_id=c.id
                    JOIN tariffs n 
                        ON a.next_tariff_id=n.id
                WHERE a.is_deleted=0
                  AND a.account_id=:basic_account";
        return $this->connection->prepare($sql);
    }

    /**
     * Тарифы для пользователя по аккаунту
     * @param $account
     * @return TariffCollection|null
     */
    public function getTariffs(int $account): ?TariffCollection
    {
        try {
            $stmt = $this->getTariffDataByAccountStmt();
            $stmt->execute([':basic_account' => $account]);
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $tariffs = new TariffCollection();
                foreach ($data as $row) {
                    $discountPeriod = new DiscountPeriod($row['discount_period'],
                        \DateTimeImmutable::createFromFormat("U", $row['discount_period_start']),
                        \DateTimeImmutable::createFromFormat("U", $row['discount_period_end']));
                    $tariff = new Tariff($row['actual_tariff'], $row['next_tariff'], $discountPeriod);
                    if(($services = $this->serviceRepository->findByTariffId($row['tariff_link'])) instanceof ServiceCollection) {
                        $tariff->setServices($services);
                    }
                    $tariffs->add($tariff);
                }
                return $tariffs;
            }
            return null;
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Tariffs query error: %message%", ['%message%' => $e->getMessage()]));
        }
    }
}
