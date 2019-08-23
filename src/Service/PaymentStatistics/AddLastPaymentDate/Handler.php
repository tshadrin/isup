<?php
declare(strict_types=1);

namespace App\Service\PaymentStatistics\AddLastPaymentDate;


use App\Collection\UTM5\RouterCollection;
use App\Entity\Statistics\LastPayment;
use App\Mapper\UTM5\PaymentMapper;
use App\ReadModel\PaymentStatistics\ExistsUsers\UserFetcher;
use App\Repository\Statistics\LastPaymentRepository;
use App\Service\UTM5\UTM5DbService;

class Handler
{
    const SAVED_PAYMENTS_COUNT = "10";
    const NO_SERVER_VALUE = "nothing";

    /**
     * @var UTM5DbService
     */
    private $UTM5DbService;
    /**
     * @var UserFetcher
     */
    private $userFetcher;
    /**
     * @var PaymentMapper
     */
    private $paymentMapper;
    /**
     * @var TableCreator
     */
    private $tableCreator;
    /**
     * @var LastPaymentRepository
     */
    private $lastPaymentRepository;

    public function __construct(UTM5DbService $UTM5DbService,
                                UserFetcher $userFetcher,
                                PaymentMapper $paymentMapper,
                                TableCreator $tableCreator,
                                LastPaymentRepository $lastPaymentRepository)
    {
        $this->UTM5DbService = $UTM5DbService;
        $this->userFetcher = $userFetcher;
        $this->paymentMapper = $paymentMapper;
        $this->tableCreator = $tableCreator;
        $this->lastPaymentRepository = $lastPaymentRepository;
    }

    public function handle(Command $command)
    {
        $this->tableCreator->createLastPaymentDateTable($command);

        $usersIds = $this->userFetcher->getExistsUsersIds();

        for ($i = 0; $i < count($usersIds); $i++) {

            $user = $this->UTM5DbService->search($usersIds[$i], UTM5DbService::SEARCH_TYPE_ID);
            $payment = $this->paymentMapper->getLastPayment($user->getAccount());
            $lp = new LastPayment($user->getId(),
                $this->getRouterName($user->getRouters()),
                !is_null($payment)?(new \DateTime())->setTimestamp($payment->getDate()->getTimestamp()):null,
                $user->isBlock(),
                $user->isJuridical()
            );
            $this->save($lp);
            if ($this->isNeedFlush($i)) {
                $this->flush();
            }
        }
        $this->flush();
    }

    private function getRouterName(?RouterCollection $routers): string
    {
        if(is_null($routers) || count($routers) === 0)
            return self::NO_SERVER_VALUE;

        return $routers[0]->getName();
    }

    private function save(LastPayment $payment): void
    {
        $this->lastPaymentRepository->save($payment);
    }

    private function isNeedFlush($num): bool
    {
        return !($num % self::SAVED_PAYMENTS_COUNT);
    }

    private function flush(): void
    {
        $this->lastPaymentRepository->flush();
    }
}