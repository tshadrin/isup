<?php
declare(strict_types=1);

namespace App\Service\OneS\GetContragent\All;

use App\Service\OneS\ReadModel\ContragentFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Handler
{
    const GROUP_ISTRANET = '907';
    const GROUP_IP_PARK = '910';
    const GROUP_ISTRA_DOT_NET = '918';

    /** @var ContragentFetcher  */
    private $contragentFetcher;
    /** @var LoggerInterface */
    private $logger;
    /** @var Request */
    private $request;

    public function __construct(ContragentFetcher $contragentFetcher, LoggerInterface $oneSLogger, RequestStack $requestStack)
    {
        $this->contragentFetcher = $contragentFetcher;
        $this->logger = $oneSLogger;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function handle(): array
    {
        $this->logger->notice("New request from {$this->request->headers->get('x-forwarded-for')} to get all contragents.");
        $result = ['contragents_count' => 0, 'contragents' => []];
        $contragentsRaw = $this->contragentFetcher->getAll();
        $this->logger->notice("Contragents found for {$this->request->headers->get('x-forwarded-for')}.");
        $contragent = [];
        foreach ($contragentsRaw as $number => $row) {
            if ($number == 0 || $contragentsRaw[$number-1]['inn'] !== $row['inn']) {
                if ($number > 0) {
                    $contragent['services_count'] = count($contragent['services']);
                    $result['contragents'][] = $contragent;
                }
                $contragent = [];
                $contragent['inn'] = $row['inn'];
                $contragent['name'] = trim($row['name']);
                if ($row['group_id'] === self::GROUP_IP_PARK) {
                    $contragent['group'] = 'ip_park';
                }
                else if ($row['group_id'] === self::GROUP_ISTRA_DOT_NET) {
                    $contragent['group'] = 'istra_dot_net';
                }
                else if ($row['group_id'] === self::GROUP_ISTRANET) {
                    $contragent['group'] = 'istranet';
                }
            } else {
                if ($row['id'] !== $contragentsRaw[$number-1]['id']) {
                    $contragent['multiple'] = true;
                }
            }
            $contragent['services'][] = [
                'service_name' => $row['service_name'],
                'cost' => $row['cost'],
                'id' => $row['id']
            ];
        }
        $result['contragents_count'] = count($result['contragents']);
        $this->logger->notice("Request for {$this->request->headers->get('x-forwarded-for')} is finished.");
        return $result;
    }
}