<?php
declare(strict_types = 1);


namespace App\Controller\PaymentsReports;


use App\ReadModel\Payments\NetPay\Filter\Filter;
use App\ReadModel\Payments\NetPay\Filter\Form;
use App\Service\NetPay\ListPayments;
use App\Service\SberbankReport\SberbankReportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NetpayReportController
 * @package App\Controller
 * @IsGranted("ROLE_SUPPORT")
 * @Route("/payments-reports/netpay", name="netpay")
 */
class NetPayController extends AbstractController
{
    const ROWS_ON_PAGE = 30;
    /**
     * @param Request $request
     * @param SberbankReportService $sberbankReportService
     * @param Session $session
     * @return Response
     * @Route("", name="", methods={"GET", "POST"})
     */
    public function index(Request $request, ListPayments\Handler $handler): Response
    {
        $filter = new Filter();
        $form = $this->createForm(Form::class, $filter);
        $form->handleRequest($request);
        $command = new ListPayments\Command(
            $filter,
            $request->query->getInt('page',1),
            self::ROWS_ON_PAGE
        );

        try {
            $payments = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('PaymentsReports\NetPay\index.html.twig', ['payments' => isset($payments)?$payments:null, 'filterForm' => $form->createView()]);
    }
}
