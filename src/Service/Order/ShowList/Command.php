<?php
declare(strict_types=1);

namespace App\Service\Order\ShowList;


use App\ReadModel\Orders\ShowList\Filter\Filter;

class Command
{
    public $filter;
    public $page;
    public $rowsOnPage;

    public function __construct(Filter $filter, int $page, int $rowsOnPage)
    {
        $this->filter = $filter;
        $this->page = $page;
        $this->rowsOnPage = $rowsOnPage;
    }
}
