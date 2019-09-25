<?php
declare(strict_types=1);

namespace App\ReadModel\UTM5;


use Doctrine\DBAL\{ Connection, Driver\Statement, FetchMode };
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;


class UserFetcher
{
    /** @var Connection  */
    private $connection;

    public function __construct(Connection $UTM5Connection)
    {
        $this->connection = $UTM5Connection;
    }

    public function getUserByPhone(string $phone): \ArrayObject
    {
        $stmt = $this->getUserByPhoneStmt();
        $stmt->execute([':phone' => $phone]);

        if(0 === $stmt->rowCount()) {
            throw new \DomainException("Not found users with phone {$phone}");
        }
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, \ArrayObject::class);
        return $stmt->fetch();
    }

    public function getUserByPhoneStmt(): Statement
    {
        $query = "SELECT id, full_name, actual_address, flat_number, basic_account
                  FROM users
                  WHERE replace(
                      replace(
                          replace(
                              replace(
                                  mobile_telephone, '(', ''
                                  ), ')', ''
                              ), '-', ''
                          ), ' ', ''
                      ) LIKE :phone";
        return $this->connection->prepare($query);
    }
}