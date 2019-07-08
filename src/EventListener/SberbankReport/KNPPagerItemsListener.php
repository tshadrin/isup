<?php
declare(strict_types=1);

namespace App\EventListener\SberbankReport;

use Knp\Component\Pager\Event\ItemsEvent;
use App\SberbankEntity\Payment;
use App\Service\SberbankReport\SberbankReportService;

/**
 * Class KNPPagerItemsListener
 * @package App\EventListener\SberbankReport
 */
class KNPPagerItemsListener
{
    /**
     * @var SberbankReportService
     */
    private $sberbankReportManager;

    /**
     * KNPPagerItemsListener constructor.
     * @param SberbankReportService $sberbankReportManager
     */
    public function __construct(SberbankReportService $sberbankReportManager)
    {
        $this->sberbankReportManager = $sberbankReportManager;
    }

    /**
     * При обработке событий устанавливаем для платежей количество записей в логе.
     * @param ItemsEvent $event
     */
    public function onKNPPagerItems(ItemsEvent $event): void
    {
        $options = $event->options;
        if (array_key_exists('entity', $options) && $options['entity'] == Payment::class) {
            $manager = $this->sberbankReportManager;
            $payments = $event->target;
            $start = $event->getOffset();
            $end = $event->getLimit() + $event->getOffset();
            for ($i = $start; ($i < $end) && ($i < count($payments)); $i++) {
                $payments[$i]->setLogCount($manager->getCountPaymentLogs($payments[$i]->getPayNum()));
            }
        }
    }
}
