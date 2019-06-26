<?php

namespace App\Repository\UTM5;

use App\Collection\UTM5\TariffCollection;
use App\Mapper\UTM5\TariffMapper;

class TariffRepository
{
    /**
     * @var TariffMapper
     */
    private $tariffMapper;

    public function __construct(TariffMapper $tariffMapper)
    {
        $this->tariffMapper = $tariffMapper;
    }

    public function findTariffByAccount(int $account): ?TariffCollection
    {
        return $this->tariffMapper->getTariffs($account);
    }
}