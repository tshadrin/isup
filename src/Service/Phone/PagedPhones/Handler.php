<?php
declare(strict_types = 1);

namespace App\Service\Phone\PagedPhones;

use App\Form\Phone\RowsForm;
use App\Repository\Phone\PhoneRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class Handler
{
    private $phoneRepository;

    private $paginator;

    public function __construct(PhoneRepository $phoneRepository, PaginatorInterface $paginator)
    {
        $this->phoneRepository = $phoneRepository;
        $this->paginator = $paginator;
    }

    public function handle(Command $command): PaginationInterface
    {
        $phones = $this->phoneRepository->getFilteredPhones($command->filter);
        if($command->rowsPerPage === RowsForm::ALL_ROWS) {
            $command->rowsPerPage = count($phones);
        }
        return $this->paginator->paginate($phones, $command->page, $command->rowsPerPage);
    }
}
