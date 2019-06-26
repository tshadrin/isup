<?php

namespace App\Collection\UTM5;

use Doctrine\Common\Collections\ArrayCollection;

class ServiceCollection extends ArrayCollection
{
    public function getCostSummary(): float
    {
        $cost = 0;
        foreach($this as $service)
            $cost += $service->getCost();
        return $cost;
    }

}