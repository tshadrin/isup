<?php
declare(strict_types=1);


namespace App\Service\OneS\ReadModel;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class ContragentFetcher
{
    /** @var Connection */
    private $UTM5Connection;

    public function __construct(Connection $UTM5Connection)
    {
        $this->UTM5Connection = $UTM5Connection;
    }

    public function getAll(): array
    {
        $sql = "SELECT u.tax_number as inn, u.full_name as name, sd.service_name, psd.cost, u.id, uggl.group_id
FROM users u
    LEFT JOIN (
        SELECT userid, value
        FROM user_additional_params uap
        WHERE uap.paramid = 11
) upp ON upp.userid = u.id
    JOIN service_links sl ON u.id = sl.user_id
    JOIN services_data sd ON sd.id = sl.service_id
    JOIN periodic_services_data psd ON psd.id = sl.service_id
    LEFT JOIN (
        SELECT ugl.group_id, ugl.user_id, `groups`.group_name
        FROM users_groups_link ugl
        JOIN `groups` ON `groups`.id = ugl.group_id
        WHERE ugl.group_id IN(910, 918, 907)) uggl ON uggl.user_id = u.id
WHERE u.is_juridical=1
  AND u.is_deleted=0
  AND sl.is_deleted=0
  AND u.tax_number<>0
  AND upp.value IS NULL
  AND psd.cost>0
ORDER BY u.tax_number, u.id, uggl.group_id";
        $stmt = $this->UTM5Connection->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new \DomainException("Results not found");
        }
        return $stmt->fetchAll();
    }

    public function getByInn(int $inn): array
    {
        $sql = "SELECT u.tax_number as inn, u.full_name as name, sd.service_name, psd.cost, u.id, uggl.group_id
FROM users u
    LEFT JOIN (
        SELECT userid, value
        FROM user_additional_params uap
        WHERE uap.paramid = 11
) upp ON upp.userid = u.id
    JOIN service_links sl ON u.id = sl.user_id
    JOIN services_data sd ON sd.id = sl.service_id
    JOIN periodic_services_data psd ON psd.id = sl.service_id
    LEFT JOIN (
        SELECT ugl.group_id, ugl.user_id, `groups`.group_name
        FROM users_groups_link ugl
        JOIN `groups` ON `groups`.id = ugl.group_id
        WHERE ugl.group_id IN(910, 918, 907)) uggl ON uggl.user_id = u.id
WHERE u.is_juridical=1
  AND u.is_deleted=0
  AND sl.is_deleted=0
  AND u.tax_number<>0
  AND upp.value IS NULL
  AND psd.cost>0
  AND u.tax_number = :inn
ORDER BY u.tax_number, u.id, uggl.group_id";
        $stmt = $this->UTM5Connection->prepare($sql);
        $stmt->execute([':inn' => $inn]);
        if ($stmt->rowCount() === 0) {
            throw new \DomainException("Results not found");
        }
        return $stmt->fetchAll();
    }

    public function checkByIdAndInn(int $id, int $inn): bool
    {
        $sql = "SELECT count(*) as count
                FROM users
                WHERE id=:id
                  AND tax_number=:inn
                  AND is_deleted=0";
        $stmt = $this->UTM5Connection->prepare($sql);
        $stmt->execute([':id' => $id, ':inn' => $inn]);

        if ((int)$stmt->fetch(FetchMode::COLUMN) === 1) {
            return true;
        }
        return false;
    }
}