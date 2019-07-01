<?php

namespace App\Mapper\UTM5;

use App\Entity\UTM5\Passport;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;
use Symfony\Contracts\Translation\TranslatorInterface;

class PassportMapper
{
    const FIELD_SERIESANDNUMBER = 'passport_series_and_number';
    const FIELD_ISSUED = 'issued';
    const FIELD_REGISTRATION = 'registrataion_address';
    const FIELD_AUTHORITYCODE = 'authority_code';
    const FIELD_BIRTHDAY = 'birthday';

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
    public function getPassportDataStmt(): Statement
    {
        $sql = "SELECT up.name, uap.value
                FROM user_additional_params uap
                    JOIN uaddparams_desc up
                        ON up.paramid = uap.paramid
                WHERE up.name IN('passport_series_and_number', 'issued', 'registrataion_address', 'authority_code', 'birthday')
                  AND uap.userid=:user_id";
        return $this->connection->prepare($sql);
    }

    /**
     * @param int $userId
     * @return Passport|null
     */
    public function getPassportById(int $userId): ?Passport
    {
        try{
            $stmt = $this->getPassportDataStmt();
            $stmt->execute([':user_id' => $userId]);
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $passport = new Passport();
                foreach ($data as $param) {
                    if(self::FIELD_SERIESANDNUMBER === $param['name']) {
                        $passport->setNumber($param['value']);
                    }
                    if(self::FIELD_REGISTRATION === $param['name']) {
                        $passport->setRegistrationAddress($param['value']);
                    }
                    if(self::FIELD_ISSUED === $param['name']) {
                        $passport->setIssued($param['value']);
                    }
                    if(self::FIELD_AUTHORITYCODE === $param['name']) {
                        $passport->setAuthorityCode($param['value']);
                    }
                    if(self::FIELD_BIRTHDAY === $param['name']) {
                        $passport->setBirthday($param['value']);
                    }
                }
                return $passport;
            }
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Check user passport query error: %message%", ['%message%' => $e->getMessage()]));
        }
        return null;
    }
}
