<?php
declare(strict_types=1);

namespace App\Command\Statistics;

use App\Service\Statistics\Payments\Add;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddPaymentsCommand extends Command
{
    const DEFAULT_ARGUMENT_VALUE = 0;
    /**
     * @var InputInterface
     */
    private $input;
    /**
     * @var OutputInterface
     */
    private $output;

    protected static $defaultName = "statistics:add-paymetns";
    /**
     * @var Add\Handler
     */
    private $handler;

    public function __construct(Add\Handler $handler, string $name = null)
    {
        parent::__construct($name);
        $this->handler = $handler;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $month = $this->getMonth();
        $year = $this->getYear();
        $command = new Add\Command($month, $year);
        $this->handler->handle($command);

    }

    protected function configure(): void
    {
        $this->setDescription("Добавить данные о платежах в статистику.")
            ->setHelp('Добавляет платежи в таблицу статистики дополняя записи о платежах.')
            ->addArgument('month', InputArgument::OPTIONAL, 'месяц (по умолчанию - прошлый)', self::DEFAULT_ARGUMENT_VALUE)
            ->addArgument('year', InputArgument::OPTIONAL, 'год (текущий по умолчанию)', self::DEFAULT_ARGUMENT_VALUE);
    }

    private function getMonth(): int
    {
        return
            $this->isMonthArgument()?
                $this->getMonthArgument():
                $this->getPreviousMonth();
    }

    private function isMonthArgument(): bool
    {
        return $this->input->getArgument('month') !== self::DEFAULT_ARGUMENT_VALUE;
    }

    private function getMonthArgument(): int
    {
        return (int)$this->input->getArgument('month');
    }

    private function getPreviousMonth(): int
    {
        return  (int)(new \DateTimeImmutable())->modify('-1 month')->format('m');
    }

    private function getYear(): int
    {
        return
            $this->isYearArgument()?
                $this->getYearArgument():
                $this->getCurrentYear();
    }

    private function isYearArgument(): bool
    {
        return $this->input->getArgument('year') !== self::DEFAULT_ARGUMENT_VALUE;
    }

    private function getYearArgument(): int
    {
        return (int)$this->input->getArgument('year');
    }

    private function getCurrentYear(): int
    {
        return (int)(new \DateTimeImmutable())->format('Y');
    }

}