<?php
declare(strict_types=1);

namespace App\Service\OneS\GetContragent\One;

use App\Service\OneS\ReadModel\ContragentFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Handler
{
    const GROUP_ISTRANET = '907';
    const GROUP_IP_PARK = '910';
    const GROUP_ISTRA_DOT_NET = '918';

    /** @var ContragentFetcher */
    private $contragentFetcher;
    /** @var LoggerInterface  */
    private $logger;
    /** @var Request */
    private $request;

    public function __construct(ContragentFetcher $contragentFetcher, LoggerInterface $oneSLogger, RequestStack $requestStack)
    {
        $this->contragentFetcher = $contragentFetcher;
        $this->logger = $oneSLogger;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function handle(Command $command): array
    {
        $this->logger->notice("New request from {$this->request->headers->get('x-forwarded-for')} to get contragent with inn {$command->inn}.");
        $contragentRaw = $this->contragentFetcher->getByInn($command->inn);
        $this->logger->notice("Contragent found for {$this->request->headers->get('x-forwarded-for')} with inn {$command->inn}.");
        $result = [];
        $result['inn'] = $contragentRaw[array_key_first($contragentRaw)]['inn'];
        $result['name'] = trim($contragentRaw[array_key_first($contragentRaw)]['name']);
        if ($contragentRaw[array_key_first($contragentRaw)]['group_id'] === self::GROUP_IP_PARK) {
            $result['group'] = 'ip_park';
        }
        else if ($contragentRaw[array_key_first($contragentRaw)]['group_id'] === self::GROUP_ISTRA_DOT_NET) {
            $result['group'] = 'istra_dot_net';
        }
        else if ($contragentRaw[array_key_first($contragentRaw)]['group_id'] === self::GROUP_ISTRANET) {
            $result['group'] = 'istranet';
        }
        $result['services'] = [];
        foreach ($contragentRaw as $number => $row) {
            if(!array_key_exists('multiple', $result) &&
                array_key_exists($number-1, $contragentRaw) &&
                $row['id'] !== $contragentRaw[$number-1]['id']) {
                $result['multiple'] = true;
            }
            $result['services'][] = [
                'service_name' => $row['service_name'],
                'cost' => $row['cost'],
                'id' => $row['id']
            ];
        }
        $result['services_count'] = count($result['services']);
        $this->logger->notice("Request for {$this->request->headers->get('x-forwarded-for')} with inn {$command->inn} is finished.");
        return $result;
    }
}