<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\FindMoney\FindMoneyService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Symfony\Component\Routing\Annotation\Route;

/**
 * Поиск всех платежей по населенному пункту
 * Class FindMoneyController
 * @package App\Controller\FindMoney
 * @IsGranted("ROLE_SUPPORT")
 */
class FindMoneyController extends AbstractController
{
    /**
     * @Route("/findmoney/", name="findmoney", methods={"GET"})
     */
    public function index(Request $request, FindMoneyService $findMoneyService): Response
    {
        //@TODO дописать форму и её нормальную обработку
        $template_data = [];
        try {
            if ($address = $request->query->get('address')) {
                $sum = $findMoneyService->findAllPaymentsSumByAddress($address);
                $template_data = ['sum' => $sum, 'address' => $address];
            }
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->render('FindMoney/index.html.twig', $template_data);
    }
}
