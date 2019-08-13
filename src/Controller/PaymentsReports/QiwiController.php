<?php
declare(strict_types = 1);


namespace App\Controller\PaymentsReports;


use App\ReadModel\Payments\Qiwi\Filter\Filter;
use App\ReadModel\Payments\Qiwi\Filter\Form;
use App\ReadModel\Payments\Qiwi\Payment;
use App\Service\Payments\Qiwi\FilteredList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QiwiController
 * @package App\Controller\PaymentsReports
 * @IsGranted("ROLE_SUPPORT")
 * @Route("/payments-reports/qiwi", name="qiwi")
 */
class QiwiController extends AbstractController
{
    const ROWS_ON_PAGE = 30;
    const DEFAULT_PAGE = 1;

    /**
     * @Route("", name="", methods={"GET", "POST"})
     */
    public function index(Request $request, FilteredList\Handler $handler): Response
    {
        $filter = new Filter();
        $form = $this->createForm(Form::class, $filter);
        $form->handleRequest($request);

        $command = new FilteredList\Command(
            $filter,
            $request->query->getInt('page',self::DEFAULT_PAGE),
            self::ROWS_ON_PAGE
        );

        try {
            $payments = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('PaymentsReports\Qiwi\index.html.twig',
            ['payments' => $payments ?? null, 'filterForm' => $form->createView()]);
    }
}