<?php

namespace App\Repository\UTM5;

use App\Entity\UTM5\Passport;
use App\Mapper\UTM5\PassportMapper;

class PassportRepository
{
    /**
     * @var PassportMapper
     */
    private $passportMapper;

    public function __construct(PassportMapper $passportMapper)
    {
        $this->passportMapper = $passportMapper;
    }

    public function findById(int $id): ?Passport
    {
        return $this->passportMapper->getPassportById($id);
    }
}
