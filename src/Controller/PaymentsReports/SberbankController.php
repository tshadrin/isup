<?php
declare(strict_types=1);

namespace App\Controller\PaymentsReports;

use App\ReadModel\Payments\Sberbank\Filter\Filter;
use App\ReadModel\Payments\Sberbank\Filter\Form;
use App\Service\Sberbank\ListPayments;
use App\Service\Sberbank\ListLog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SberbankController
 * @package App\Controller\PaymentsReports
 * @IsGranted("ROLE_SUPPORT")
 * @Route("/paymnet-reports/sberbank", name="sberbank")
 */
class SberbankController extends AbstractController
{
    const ROWS_ON_PAGE = 30;
    const DEFAULT_PAGE = 1;

    /**
     * @Route("", name="", methods={"GET", "POST"})
     */
    public function index(Request $request, ListPayments\Handler $handler): Response
    {
        $filter = new Filter();
        $form = $this->createForm(Form::class, $filter);
        $form->handleRequest($request);

        $command = new ListPayments\Command(
            $filter,
            $request->query->getInt('page', self::DEFAULT_PAGE),
            self::ROWS_ON_PAGE
        );

        try {
            $payments = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('PaymentsReports/Sberbank/index.html.twig',
            ['payments' => $payments ?? null, 'filterForm' => $form->createView(),]);
    }


    /**
     * @param int $transaction
     * @param ListLog\Handler $handler
     * @return Response
     * @Route("/{transaction}", name=".log", methods={"GET"}, requirements={"transaction": "\d+"})
     */
    public function paymentLog(int $transaction, ListLog\Handler $handler): Response
    {
        $command = new ListLog\Command($transaction);

        try {
            $logRows = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('PaymentsReports/Sberbank/logs.html.twig',
            ['transaction' => $transaction, 'logRows' => $logRows ?? null]);
    }
}
