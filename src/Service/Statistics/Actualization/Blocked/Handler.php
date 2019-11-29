<?php
declare(strict_types=1);

namespace App\Service\Statistics\Actualization\Blocked;

use App\Entity\UTM5\Payment;
use App\Entity\UTM5\Tariff;
use App\Entity\UTM5\UTM5User;
use App\ReadModel\Statistics\ActualizationFetcher;
use App\Service\UTM5\UTM5DbService;

class Handler
{
    /** @var UTM5DbService */
    private $UTM5DbService;
    /** @var ActualizationFetcher */
    private $actualizationFetcher;
    private $dir;

    public function __construct(UTM5DbService $UTM5DbService, ActualizationFetcher $actualizationFetcher, array $addFirmParameters)
    {
        $this->UTM5DbService = $UTM5DbService;
        $this->actualizationFetcher = $actualizationFetcher;
        $this->dir = $addFirmParameters['files_dir'];
    }

    public function handle(Command $command): void
    {
        /** @var UserDTO[] $users */
        $users = $this->actualizationFetcher->getUsersInfoWithoutFilter();
        $user = new UserDTO();
        $h = fopen($this->dir."/report_blocked.csv", "w");
        fputcsv($h, ["ID", "ФИО", "Телефон", "Email", "Тарифы", "Адрес", "Баланс"], ";");
        foreach($users as $num => $user) {
            if (!is_null($user->flat_number)) {
                $user->address = "{$user->address} - {$user->flat_number}";
            }
            if (!empty($user->mobile) && !empty($users->home)) {
                $user->phone = "{$user->mobile}, {$user->home}";
            }
            if (!empty($user->mobile) && empty($users->home)) {
                $user->phone = $user->mobile;
            }
            if (empty($user->mobile) && !empty($users->home)) {
                $user->phone = $user->home;
            }
            /** @var UTM5User $u */
            $u = $this->UTM5DbService->search($user->id);
            /** @var Tariff[] $tariffs */
            $tariffs = $u->getTariffs();
            if(!is_null($tariffs)) {
                foreach ($tariffs as $k => $tariff) {
                    if ($k === 0) {
                        $user->tariffs = $tariff->getName();
                    } else {
                        $user->tariffs .= ", {$tariff->getName()}";
                    }
                }
            } else {
                $user->tariffs = UserDTO::NO_TARIFFS;
            }
            /** @var Payment[] $payments */
            $payments = $u->getPayments();
            if(!is_null($payments)) {
                foreach ($payments as $payment) {
                    if ($payment->getAmount() > 0 && $payment->getAdminComment() !== "обнуление") {
                        $pay_date = $payment->getDate()->setTimezone( new \DateTimeZone("Europe/Moscow"));
                        if ($pay_date <= ($this->getDateNowMonth())) {
                            fputcsv($h, [$user->id, $user->fullname, $user->phone, $user->email, $user->tariffs, $user->address, round($user->balance,2)], ";");
                        }
                        break;
                    } else {
                        continue;
                    }
                }
            } else {
                fputcsv($h, [$user->id, $user->fullname, $user->phone, $user->email, $user->tariffs, $user->address,  round($user->balance,2)], ";");
            }
        }
    }

    public function getDateNowMonth(): \DateTimeImmutable
    {
        $date =  (new \DateTimeImmutable())->setTime(0,0,0);
        return $date->setDate((int)$date->format("Y"),(int)$date->format("m"), 1)->modify("-3 month")->modify("-4 days");
    }
}