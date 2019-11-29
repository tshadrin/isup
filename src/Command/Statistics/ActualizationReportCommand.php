<?php
declare(strict_types=1);

namespace App\Command\Statistics;

use App\Service\Statistics\Actualization;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActualizationReportCommand extends Command
{
    protected static $defaultName = "statistics:actualization";

    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var Actualization\Handler  */
    private $handler;

    public function __construct(Actualization\Handler $handler, string $name = null)
    {
        parent::__construct($name);
        $this->handler = $handler;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;
        $this->handler->handle(new Actualization\Command());
    }
}