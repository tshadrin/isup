<?php
declare(strict_types = 1);


namespace App\Service\Phone\PagedPhones;


use App\Form\Phone\Filter;

class Command
{
    public $filter;
    public $rowsPerPage;
    public $page;

    public function __construct(Filter $filter, int $page, int $rowsPerPage)
    {
        $this->filter = $filter;
        $this->page = $page;
        $this->rowsPerPage = $rowsPerPage;
    }
}