<?php
declare(strict_types = 1);

namespace App\Service\Phone\PagedPhones;

use App\Form\RowsForm;
use App\Repository\Phone\PhoneRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class Handler
{
    /**
     * @var PhoneRepository
     */
    private $phoneRepository;
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * Handler constructor.
     * @param PhoneRepository $phoneRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(PhoneRepository $phoneRepository, PaginatorInterface $paginator)
    {
        $this->phoneRepository = $phoneRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param Command $command
     * @return PaginationInterface
     */
    public function handle(Command $command): PaginationInterface
    {
        $phones = $this->phoneRepository->getFilteredPhones($command->filter);
        if ($command->rowsOnPage === RowsForm::ALL_ROWS_ON_PAGE) {
            $command->rowsOnPage = count($phones);
        }
        return $this->paginator->paginate($phones, $command->page, $command->rowsOnPage);
    }
}
