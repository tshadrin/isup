<?php
declare(strict_types = 1);


namespace App\Service\NetPay\ListPayments;


use App\ReadModel\Payments\NetPay\Filter\Filter;

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
