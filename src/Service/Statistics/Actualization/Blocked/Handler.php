<?php
declare(strict_types=1);

namespace App\Service\Statistics\Actualization\Blocked;

use App\Entity\UTM5\Payment;
use App\Entity\UTM5\Tariff;
use App\Entity\UTM5\UTM5User;
use App\ReadModel\Statistics\ActualizationFetcher;
use App\Service\UTM5\UTM5DbService;
use Doctrine\Common\Collections\ArrayCollection;

class Handler
{
    private const CSV_DELIMITER = ';';
    private const FILEDS_NAMES = ["ID", "ФИО", "Телефон", "Email", "Тарифы", "Адрес", "Баланс"];

    /** @var UTM5DbService */
    private $UTM5DbService;
    /** @var ActualizationFetcher */
    private $actualizationFetcher;
    private $filesDir;
    private $csvHandler;
    private $minimalDate;

    public function __construct(UTM5DbService $UTM5DbService, ActualizationFetcher $actualizationFetcher, array $addFirmParameters)
    {
        $this->UTM5DbService = $UTM5DbService;
        $this->actualizationFetcher = $actualizationFetcher;
        $this->filesDir = $addFirmParameters['files_dir'];
        $this->minimalDate = $this->getFourMonthAgoDate();
    }

    public function handle(Command $command): void
    {
        /** @var UserDTO[] $users */
        $users = $this->actualizationFetcher->getUsersInfoWithoutFilter();

        $this->setupCsvHandler();
        $this->writeCsvRow(self::FILEDS_NAMES);

        foreach ($users as $user) {
            $user->setupAddress();
            $user->setupPhone();

            /** @var UTM5User $u */
            $u = $this->UTM5DbService->search($user->id);

            /** @var Tariff[] $tariffs */
            if (!is_null($tariffs = $u->getTariffs())) {
                foreach ($tariffs as $k => $tariff) {
                    $user->tariffs = 0 === $k ? $tariff->getName(): ", {$tariff->getName()}";
                }
            } else {
                $user->tariffs = UserDTO::NO_TARIFFS;
            }

            /** @var ArrayCollection $payments */
            $payments = $u->getPayments();
            if (is_null($payments) || $this->hasLastPaymentBeforeMinimalDate($payments)) {
                $this->writeCsvRow([
                    $user->id,
                    $user->fullname,
                    $user->phone,
                    $user->email,
                    $user->tariffs,
                    $user->address,
                    round($user->balance, 2),
                    ]);
            }
        }
    }

    public function getFourMonthAgoDate(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->setTime(0,0,0)->modify("-4 month");
    }

    public function setupCsvHandler(): void
    {
        if (!($this->csvHandler = fopen("{$this->filesDir}/report_blocked.csv", "w"))) {
            throw new \DomainException("Error init csv file handler");
        }
    }

    public function writeCsvRow(array $fields): void
    {
        if(count($fields) !== count(self::FILEDS_NAMES))  {
            throw new \DomainException("Not all fields set");
        }
        fputcsv($this->csvHandler, $fields, self::CSV_DELIMITER);
    }

    public function hasLastPaymentBeforeMinimalDate(ArrayCollection $payments): bool
    {
        /** @var Payment $payment */
        foreach ($payments as $payment) {
            if ($payment->getAmount() > 0 && $payment->getAdminComment() !== "обнуление") {
                if ($payment->getDate()->setTimezone( new \DateTimeZone("Europe/Moscow")) <= $this->minimalDate) {
                    return true;
                } else {
                    return false;
                }
            } else {
                continue;
            }
        }
        //если не было положительных платежей
        return true;
    }
}