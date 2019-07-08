<?php
declare(strict_types=1);

namespace App\Command\UTM5;

use App\Service\UTM5\UTM5DbService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{ InputArgument, InputInterface };
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AddFirmForAcquiroPaymentsCommand
 * @package App\Command\UTM5
 */
class AddFirmForAcquiroPaymentsCommand extends Command
{
    /**
     * @var string
     */
    protected $files_dir;

    /**
     * @var int
     */
    protected $ippark_group;

    /**
     * @var UTM5DbService
     */
    private $UTM5DbService;

    /**
     * AddFirmForAcquiroPaymentsCommand constructor.
     * @param array $parameters
     * @param UTM5DbService $UTM5DbService
     */
    public function __construct(array $parameters, UTM5DbService $UTM5DbService)
    {
        $this->files_dir = $parameters['files_dir'];
        $this->ippark_group = $parameters['ippark_group'];
        $this->UTM5DbService = $UTM5DbService;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $f = new Filesystem();
        $filename = $input->getArgument('file_name');
        $filepath = $this->files_dir . DIRECTORY_SEPARATOR . $filename;
        $new_filename = "new_{$filename}";
        $new_filepath = $this->files_dir . DIRECTORY_SEPARATOR . $new_filename;

        if ($f->exists($filepath)) {
            $output->writeln("Файл найден, начинаем работу.");
            if(false !== ($handler = fopen($filepath, 'r'))) {
                if(false !== ($handler_new = fopen($new_filepath, 'w'))) {
                    $fields = fgetcsv($handler, 9000, ';');  // 0 - sum 3 - email 6 - id 1 - date
                    fputcsv($handler_new, [$fields[0], $fields[3],$fields[6], $fields[1], 'Фирма'], ';');
                    while (false !== ($data = fgetcsv($handler, 9000, ';'))) {
                        $user = $this->UTM5DbService->search($data[6]);
                        $csv_group = false;
                        foreach ($user->getGroups() as $group) {
                            if ($group['id'] == $this->ippark_group) //910 группа это ай пи парк
                                $csv_group = "Ай Пи Парк";
                        }
                        if(false === fputcsv($handler_new, [$data[0], $data[3],$data[6], $data[1], $csv_group?'Ай Пи Парк':''], ';')) {
                            fclose($handler);
                            fclose($handler_new);
                            throw new \DomainException("Ошибка записи в новый файл!");
                        }
                    }
                }
            }
            fclose($handler);
            fclose($handler_new);
            $output->writeln("Обработка завершена.");
        } else {
            $output->writeln("Ошибка. Файл не найден.");
        }
    }


    protected function configure(): void
    {
        $this->setDescription("Добавить название фирмы в платежи.")
            ->setHelp('Добавляет поле с названием фирмы для csv списка платежей.')
            ->addArgument('file_name', InputArgument::REQUIRED, 'CSV file name');
    }
}
