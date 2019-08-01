<?php
declare(strict_types=1);

namespace App\Command\Commutator;

use App\Service\UTM5\UTM5DbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseNagios extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Папка с файлами отчетов
     * @var mixed
     */
    private $files_dir;

    /**
     * AddFirmForAcquirPaymentsCommand constructor.
     * @param string $files_dir
     * @param UTM5DbService $UTM5DbService
     */
    public function __construct(array $parameters, EntityManagerInterface $entityManager)
    {
        $this->files_dir = $parameters['files_dir'];
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $work_file = $input->getArgument('file_name');
        $group =  $input->getArgument('group');
        //$work_file=	"adasko2_2.cfg"; # база
        $in_group = false;
        $in_service = false;
        $in_host = false;
        $ip = '';
        $link = '/switchinfo/show';
        $switch_name = '';
        $action_url = false;
        $result = '';
        $hostgroup = '';
        $hostgroups = false;
        $notes= false;
        if(false !== ($handler = fopen($this->files_dir.DIRECTORY_SEPARATOR.$work_file,'r'))) {
            while (false !== ($string = fgets($handler, 104876))) {
                $string = trim($string);
                if("define hostgroup{" === $string) {
                    $in_group = true;
                }
                if("define service{" === $string) {
                    $in_service = true;
                }
                if("define host{" === $string) {
                    $in_host = true;
                }
                if($in_group || $in_service) {
                    if("}" === $string) {
                        $in_group = false;
                        $in_service = false;
                    }
                    if(!(("define service{" === $string) ||
                        ("define hostgroup{" === $string) ||
                        ("}" === $string)))
                        $result .= "    ";
                    $result .= "{$string}\n";
                    if($in_group)
                        $hostgroup .= "{$string}\n";
                }
                if($in_host) {
                    if("}" === $string) {
                        $in_host = false;
                        if(false === $action_url) {
                            $result .= "    action_url         http://{$ip}/\n";
                        }
                        if(false === $hostgroups) {
                            if(mb_ereg_match(".*$switch_name", $hostgroup)){
                                $result .= "    hostgroups         {$group}\n";
                            }
                        }
                        if(false === $notes) {
                            $result .= "notes_url         {$link}/{$ip}/\n";
                        }
                        $notes = false;
                        $hostgroups = false;
                        $action_url = false;
                    }
                    if(!(("define host{" === $string) || ("}" === $string)))
                        $result .= "    ";
                    if(mb_ereg_match("address", $string)) {
                        $data = explode(" ", $string);
                        if(($c = count($data)) > 1)
                            $ip = trim($data[$c-1]);
                        else {
                            $data = explode("\t", $string);
                            if (($c = count($data)) > 1)
                                $ip = trim($data[$c - 1]);
                            else
                                die;
                        }
                    }
                    if(!mb_ereg_match("notes_url", $string)) {
                        $result .= "{$string}\n";
                    } else {
                        $notes = true;
                        $result .= "notes_url         {$link}/{$ip}/\n";
                    }
                    if(mb_ereg_match("action_url", $string)){
                        $action_url = true;
                    }
                    if(mb_ereg_match("hostgroups", $string)){
                        $hostgroups = true;
                    }
                    if(mb_ereg_match("host_name", $string)){
                        $data = explode(" ", $string);
                        if(($c = count($data)) > 1)
                            $switch_name = trim($data[$c-1]);
                        else {
                            $data = explode("\t", $string);
                            if (($c = count($data)) > 1)
                                $switch_name = trim($data[$c - 1]);
                            else
                                die;
                        }
                    }
                }
            }
            print $result;
            //$this->entityManager->flush();
            fclose($handler) or die($php_errormsg);
        } else {
            throw new \RuntimeException("Ошибка при открытии файла");
        }
    }
}
