<?php
declare(strict_types = 1);


namespace App\Service\UTM5;


class DealData
{
    public $id;
    public $status;
    public $utm5Id;

    public function __construct(int $id, string $status, int $utm5Id)
    {
        $this->status = $status;
        $this->utm5Id = $utm5Id;
        $this->id = $id;
    }
}