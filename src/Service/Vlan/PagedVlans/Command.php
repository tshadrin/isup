<?php
declare(strict_types = 1);


namespace App\Service\Vlan\PagedVlans;

use App\Form\Vlan\DTO\Filter;

class Command
{
    /**
     * @var Filter
     */
    public $filter;
    /**
     * @var int
     */
    public $rowsOnPage;
    /**
     * @var int
     */
    public $page;

    /**
     * Command constructor.
     * @param Filter $filter
     * @param int $page
     * @param int $rowsOnPage
     */
    public function __construct(Filter $filter, int $page, int $rowsOnPage)
    {
        $this->filter = $filter;
        $this->page = $page;
        $this->rowsOnPage = $rowsOnPage;
    }
}
