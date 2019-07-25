<?php
declare(strict_types=1);

namespace App\Controller\SberbankReport;

use App\SberbankEntity\Payment;
use App\Form\SberbankReport\PaymentFilterForm;
use App\Service\SberbankReport\SberbankReportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SberbankReportController
 * @package MainBundle\Controller\SberbankReport
 * @IsGranted("ROLE_SUPPORT")
 */
class SberbankReportController extends AbstractController
{
    /**
     * Контроллер показывает список платежей постранично
     * @param Request $request
     * @param SberbankReportService $sberbankReportService
     * @param Session $session
     * @return Response
     * @Route("/sberbank/", name="sberbank_report_index", methods={"GET", "POST"})
     */
    public function index(Request $request, SberbankReportService $sberbankReportService): Response
    {
        $form = $this->createForm(PaymentFilterForm::class, new Payment());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $payments = $sberbankReportService->getPaymentsByDateRange($data, $request->query->getInt('page', 1));
        } else {
            foreach($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            $payments = $sberbankReportService->getLastPayments($request->query->getInt('page', 1));
        }
        return $this->render('SberbankReport/index.html.twig', ['payments' => $payments, 'form' => $form->createView(),]);
    }

    /**
     * Контроллер показа логов для платежа
     * @param int $pay_num
     * @param SberbankReportService $sberbankReportService
     * @return Response
     * @Route("/sberbank/log/{pay_num}", name="sberbank_log", methods={"GET"}, requirements={"pay_num": "\d+"})
     */
    public function paymentLog(int $pay_num, SberbankReportService $sberbankReportService): Response
    {
        return $this->render('SberbankReport/more.html.twig', ['info' => $sberbankReportService->getPaymentLog($pay_num)]);
    }
}
