<?php
declare(strict_types=1);

namespace App\Command\Statistics;

use App\Service\PaymentStatistics\AddLastPaymentDate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddLastPaymentDateCommand extends Command
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

    protected static $defaultName = "statistics:add-last-payment-date";
    /**
     * @var AddLastPaymentDate\Handler
     */
    private $handler;

    public function __construct(AddLastPaymentDate\Handler $handler, string $name = null)
    {
        parent::__construct($name);
        $this->handler = $handler;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $command = new AddLastPaymentDate\Command($this->getMonth(), $this->getYear());
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
        return  (int)(new \DateTimeImmutable())->format('m');
    }

    private function getYear(): int
    {
        return (int)(new \DateTimeImmutable())->format('Y');
    }

}