<?php

namespace App\Repository\UTM5;

use App\Collection\UTM5\ServiceCollection;
use App\Mapper\UTM5\ServiceMapper;

class ServiceRepository
{
    /**
     * @var ServiceMapper
     */
    private $serviceMapper;

    public function __construct(ServiceMapper $serviceMapper)
    {
        $this->serviceMapper = $serviceMapper;
    }

    public function findByAccount(int $account): ?ServiceCollection
    {
        return $this->serviceMapper->getServices($account);
    }

    public function findByTariffId(int $tariff_id): ?ServiceCollection
    {
        return $this->serviceMapper->getTariffServices($tariff_id);
    }
}