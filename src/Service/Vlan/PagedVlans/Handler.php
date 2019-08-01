<?php
declare(strict_types = 1);

namespace App\Service\Vlan\PagedVlans;

use App\Form\Phone\RowsForm;
use App\Repository\Vlan\VlanRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class Handler
{
    /**
     * @var VlanRepository
     */
    private $vlanRepository;
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * Handler constructor.
     * @param VlanRepository $phoneRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(VlanRepository $vlanRepository, PaginatorInterface $paginator)
    {
        $this->vlanRepository = $vlanRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param Command $command
     * @return PaginationInterface
     */
    public function handle(Command $command): PaginationInterface
    {
        $vlans = $this->vlanRepository->getFilteredVlans($command->filter);
        if ($command->rowsOnPage === RowsForm::ALL_ROWS_ON_PAGE) {
            $command->rowsOnPage = count($vlans);
        }
        return $this->paginator->paginate($vlans, $command->page, $command->rowsOnPage);
    }
}
