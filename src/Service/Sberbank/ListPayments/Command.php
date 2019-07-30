<?php
declare(strict_types = 1);


namespace App\Service\Sberbank\ListPayments;


use App\ReadModel\Payments\Sberbank\Filter\Filter;

class Command
{
    public $filter;
    public $rowsOnPage;
    public $page;

    public function __construct(Filter $filter, int $page, int $rowsOnPage)
    {
        $this->filter = $filter;
        $this->page = $page;
        $this->rowsOnPage = $rowsOnPage;
    }
}