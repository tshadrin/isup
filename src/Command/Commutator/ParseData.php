<?php

namespace App\Command\Commutator;

use App\Entity\Commutator\Port;
use App\Service\UTM5\UTM5DbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseData extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $work_file=	"all.txt"; # база
        $in_sw = true;
        $commutator = '';
        if(false !== ($handler = fopen($this->files_dir.DIRECTORY_SEPARATOR.$work_file,'r'))) {
            while (false !== ($string = fgets($handler, 104876))) {
                list($name, $model, $ip, $mac, $notes) = explode("|", $string);
                if($notes !== '') {
                    $in_sw = true;
                } else {
                    $port = new Port();
                    $port->setNumber($name);
                    $port->setDescription($model);
                    $port->setSpeed($ip);
                    if($mac === "оптика") {
                        $port_type = $this->entityManager->getRepository("App\Entity\Commutator\PortType")
                            ->findOneBy(['name' => 'optical']);
                        $port->setType($port_type);
                    }
                    if($mac === "медь") {
                        $port_type = $this->entityManager->getRepository("App\Entity\Commutator\PortType")
                            ->findOneBy(['name' => 'copper']);
                        $port->setType($port_type);
                    }
                    $commutator->addPort($port);
                    $this->entityManager->persist($commutator);
                }
                if($in_sw)
                {
                    $commutator = $this->entityManager
                        ->getRepository("App\Entity\Commutator\Commutator")
                        ->getByIP($ip);
                    $in_sw = false;
                }
            }
            $this->entityManager->flush();
            fclose($handler) or die($php_errormsg);
        } else {
            throw new \RuntimeException("Ошибка при открытии файла");
        }
    }
}
