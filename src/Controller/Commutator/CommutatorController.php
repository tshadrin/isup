<?php
declare(strict_types=1);

namespace App\Controller\Commutator;

use App\Repository\Commutator\CommutatorRepository;
use App\Service\Commutator\BotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommutatorController
 * @package App\Controller\Commutator
 */
class CommutatorController extends AbstractController
{
    /**
     * @param $ip
     * @param CommutatorRepository $commutatorRepository
     * @param BotService $botService
     * @return Response
     * @Route("/switchinfo/show/{ip}", name="switch_info_show", methods={"GET"}, requirements={"ip": "^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z"})
     */
    public function show(string $ip, CommutatorRepository $commutatorRepository, BotService $botService): Response
    {
        $data = [];
        try {
            $commutator = $commutatorRepository->getByIP($ip);
            $data['commutator'] = $commutator;
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        try{
            $switch_log = $botService->getCommutatorData($ip);
            $data['bot_data'] = $switch_log;
            $data['log'] = $switch_log['log'];
            $data['model'] = $switch_log['model'];
            $data['config_path'] = $switch_log['config_path'];
            $data['map_image_url'] = $switch_log['map_image_url'];

        } catch (\DomainException $e){
            $this->addFlash('error', $e->getMessage());
        }
        return $this->render("Commutator/index.html.twig", $data);
    }
}
