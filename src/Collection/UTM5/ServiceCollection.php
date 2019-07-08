<?php
declare(strict_types=1);

namespace App\Collection\UTM5;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ServiceCollection
 * @package App\Collection\UTM5
 */
class ServiceCollection extends ArrayCollection
{
    /**
     * @return float
     */
    public function getCostSummary(): float
    {
        $cost = 0;
        foreach($this as $service)
            $cost += $service->getCost();
        return $cost;
    }
}
