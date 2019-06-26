<?php

namespace App\Repository\UTM5;

use App\Entity\UTM5\House;
use App\Mapper\UTM5\HouseMapper;

class HouseRepository
{
    /**
     * @var HouseMapper
     */
    private $houseMapper;

    public function __construct(HouseMapper $houseMapper)
    {
        $this->houseMapper = $houseMapper;
    }

    /**
     * @param int $id
     * @return House
     */
    public function findOneById(int $id): House
    {
        return $this->houseMapper->getHouse($id);
    }
}