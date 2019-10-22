<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\OneS\GetContragent\All;
use App\Service\OneS\GetContragent\One;
use App\Service\OneS\Payment;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OneSController
 * @package App\Controller
 * @Route("/1c", name="1c")
 */
class OneSController extends AbstractController
{
    /**
     * @return JsonResponse
     * @Route("/all", name=".all", methods={"GET"})
     */
    public function getAll(All\Handler $handler, Request $request, LoggerInterface $oneSLogger): JsonResponse
    {
        try {
            $contragents = $handler->handle();
            return $this->json(['result' => 'success', 'data' => $contragents]);
        } catch (\Exception | \DomainException $e) {
            $oneSLogger->error("Request from {$request->headers->get('x-forwarded-for')} failed with error: {$e->getMessage()}");
            return $this->json(['result' => 'error', 'description' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @return JsonResponse
     * @Route("/{inn}", name=".inn", methods={"GET"}, requirements={"inn": "\d+"} )
     */
    public function getOne(int $inn, One\Handler $handler, Request $request, LoggerInterface $oneSLogger): JsonResponse
    {
        try {
            $contragent = $handler->handle(new One\Command($inn));
            return $this->json(['result' => 'success', 'data' => $contragent]);
        } catch (\Exception | \DomainException $e) {
            $oneSLogger->error("Request from {$request->headers->get('x-forwarded-for')} with inn {$inn} failed with error: {$e->getMessage()}");
            return $this->json(['result' => 'error', 'description' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @return JsonResponse
     * @Route("/pay", name=".pay", methods={"POST"})
     */
    public function pay(Request $request, Payment\Add\Handler $handler, LoggerInterface $oneSLogger): JsonResponse
    {
        try {
            $command = new Payment\Add\Command(
                $request->request->getInt('id', 0),
                $request->request->getInt('inn', 0),
                $request->request->getInt('amount', 0.0)
            );
            $handler->handle($command);
            return $this->json(['result' => 'success']);
        } catch (\Exception | \DomainException | \InvalidArgumentException $e ) {
            $oneSLogger->error("Request from {$request->headers->get('x-forwarded-for')} with inn {$request->request->getInt('inn')} and {$request->request->getInt('id')} and amount {$request->request->getInt('amount')} failed with error: {$e->getMessage()}");
            return $this->json(['result' => 'error', 'description' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}