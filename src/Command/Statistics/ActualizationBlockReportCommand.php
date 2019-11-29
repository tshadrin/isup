<?php
declare(strict_types=1);

namespace App\Command\Statistics;

use App\Service\Statistics\Actualization\Blocked;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActualizationBlockReportCommand extends Command
{
    protected static $defaultName = "statistics:act-blocked";

    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var Blocked\Handler  */
    private $handler;

    public function __construct(Blocked\Handler $handler, string $name = null)
    {
        parent::__construct($name);
        $this->handler = $handler;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;
        $this->handler->handle(new Blocked\Command());
    }
}