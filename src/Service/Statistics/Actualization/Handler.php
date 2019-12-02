<?php
declare(strict_types=1);

namespace App\Service\Statistics\Actualization;

use App\Entity\UTM5\Payment;
use App\Entity\UTM5\Tariff;
use App\Entity\UTM5\UTM5User;
use App\ReadModel\Statistics\ActualizationFetcher;
use App\Service\UTM5\UTM5DbService;

class Handler
{
    private const CSV_DELIMITER = ";";
    private const FILEDS_NAMES =  ["ID", "ФИО", "Телефон", "Email", "Тарифы", "Адрес", "Последний платеж", "Поле сортировки"];
    /** @var UTM5DbService */
    private $UTM5DbService;
    /** @var ActualizationFetcher */
    private $actualizationFetcher;
    private $filesDir;
    private $csvHandler;

    public function __construct(UTM5DbService $UTM5DbService, ActualizationFetcher $actualizationFetcher, array $addFirmParameters)
    {
        $this->UTM5DbService = $UTM5DbService;
        $this->actualizationFetcher = $actualizationFetcher;
        $this->filesDir = $addFirmParameters['files_dir'];
    }

    public function handle(Command $command): void
    {
        /** @var UserDTO[] $users */
        $users = $this->actualizationFetcher->getUsersInfoByFilter();

        $this->setupCsvHandler();
        $this->writeCsvRow(self::FILEDS_NAMES);

        foreach ($users as $num => $user) {
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

            /** @var Payment[] $payments */
            $payments = $u->getPayments();
            if (!is_null($payments)) {
                foreach ($payments as $payment) {
                    if ($payment->getAmount() > 0 && $payment->getAdminComment() !== "обнуление") {
                        $pay_date = $payment->getDate()->setTimezone( new \DateTimeZone("Europe/Moscow"));
                        if ($pay_date >= ($now_month = $this->getDateNowMonth())) {
                            $user->group = UserDTO::GROUP_NOV_MONTH;
                            $user->month = 1;
                        } else if ($pay_date >= $now_month->modify("-1 month")) {
                            $user->group = UserDTO::GROUP_OCT_MONTH;
                            $user->month = 2;
                        } else if ($pay_date >= $now_month->modify("-2 month")) {
                            $user->group = UserDTO::GROUP_SEP_MONTH;
                            $user->month = 3;
                        } else if ($pay_date >= $now_month->modify("-3 month")) {
                            $user->group = UserDTO::GROUP_AUG_MONTH;
                            $user->month = 4;
                        } else {
                            $user->group = UserDTO::GROUP_MANY_MONTH;
                            $user->month = 5;
                        }
                        break;
                    }
                }
            } else {
                $user->group = UserDTO::GROUP_MANY_MONTH;
                $user->month = 5;
            }
            $this->writeCsvRow([$user->id, $user->fullname, $user->phone, $user->email, $user->tariffs, $user->address, $user->group, $user->month]);
        }
    }

    public function getDateNowMonth(): \DateTimeImmutable
    {
        $date =  (new \DateTimeImmutable())->setTime(0,0,0);
        return $date->setDate((int)$date->format("Y"), (int)$date->format("m"), 1);
    }

    public function setupCsvHandler(): void
    {
        if (!($this->csvHandler = fopen("{$this->filesDir}/report_blocked.csv", "w"))) {
            throw new \DomainException("Error init csv file handler");
        }
    }

    public function writeCsvRow(array $fields): void
    {
        if (count($fields) !== count(self::FILEDS_NAMES))  {
            throw new \DomainException("Not all fields set");
        }
        fputcsv($this->csvHandler, $fields, self::CSV_DELIMITER);
    }
}