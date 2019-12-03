<?php
declare(strict_types = 1);

namespace App\Command\UTM5;

use App\Service\UTM5\UTM5DbService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GetUsersByIPList extends Command
{
    protected static $defaultName="tv:get-users-from-list";
    /** @var string  */
    protected $files_dir;
    /** @var UTM5DbService  */
    protected $UTM5DbService;

    /**
     * AddFirmForAcquirPaymentsCommand constructor.
     * @param string $files_dir
     * @param UTM5DbService $UTM5DbService
     */
    public function __construct(array $addFirmParameters, UTM5DbService $UTM5DbService)
    {
        $this->files_dir = $addFirmParameters['files_dir'];
        $this->UTM5DbService = $UTM5DbService;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $f = new Filesystem();
        $filename = $input->getArgument('file_name');
        $filepath = $this->files_dir.DIRECTORY_SEPARATOR.$filename;
        $new_filename = "new_{$filename}";
        $new_filepath = $this->files_dir . DIRECTORY_SEPARATOR . $new_filename;
        if ($f->exists($filepath)) {
            $output->writeln("Файл найден, начинаем работу.");
            if(false !== ($handler = fopen($filepath, 'r'))) {
                if(false !== ($handler_new = fopen($new_filepath, 'w'))) {

                    while(false !== ($ip = fgets($handler))) {
                        $output = [];
                        $output[] = trim($ip);
                        try {
                            $user = $this->UTM5DbService->search(trim($ip), 'ip');
                            $output[] = $user->getId();
                            $output[] = $user->getFullName();
                            $output[] = $user->getMobilePhone();
                            foreach($user->getTariffs() as $tariff) {
                                $output[] = $tariff->getName();
                            }
                        } catch (\Exception $e) {
                            $output[] = $e->getMessage();
                        }
                        fputcsv($handler_new, $output, ';');
                    }
                    /*
                    fputs($handler_new, $fields);
                    while (false !== ($data = fgetcsv($handler, 9000, ';'))) {
                        list($param_name, $utm5_id) = explode('=', "{$data[8]}=");
                        $user = $this->UTM5DbService->search($utm5_id);
                        $csv_group = false;
                        foreach ($user->getGroups() as $group) {
                            if ($group['id'] == $this->ippark_group) //910 группа это ай пи парк
                                $csv_group = "Ай Пи Парк";
                        }
                        if(false === fputcsv($handler_new, [$data[4], $data[5],$utm5_id, $data[6], $csv_group?'Ай Пи Парк':''], ';')) {
                            fclose($handler);
                            fclose($handler_new);
                            throw new \DomainException("Ошибка записи в новый файл!");
                        }
                    }
                    */
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
        $this->setDescription("Выдает список с данными пользователей по списку ip адресов")
            ->setHelp('Выдает данные пользователей по ip адресам.')
            ->addArgument('file_name', InputArgument::REQUIRED, 'txt file name');
    }
}