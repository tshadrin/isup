<?php
declare(strict_types = 1);


namespace App\Service\Payments\Qiwi\FilteredList;


use App\ReadModel\Payments\Qiwi\Filter\Filter;

class Command
{
    /**
     * @var Filter
     */
    public $filter;
    /**
     * @var int
     */
    public $page;
    /**
     * @var int
     */
    public $rowsOnPage;

    public function __construct(Filter $filter, int $page, int $rowsOnPage)
    {
        $this->filter = $filter;
        $this->page = $page;
        $this->rowsOnPage = $rowsOnPage;
    }
}