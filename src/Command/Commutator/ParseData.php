<?php
declare(strict_types=1);

namespace App\Command\Commutator;

use App\Entity\Commutator\Port;
use App\Service\UTM5\UTM5DbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParseData
 * @package App\Command\Commutator
 */
class ParseData extends Command
{
    protected static $defaultName = 'commutator:add-switches';

    /** @var EntityManagerInterface  */
    private $entityManager;
    /** @var string */
    private $files_dir;


    /**
     * AddFirmForAcquirPaymentsCommand constructor.
     * @param string $files_dir
     * @param UTM5DbService $UTM5DbService
     */
    public function __construct(array $addFirmParameters, EntityManagerInterface $entityManager)
    {
        $this->files_dir = $addFirmParameters['files_dir'];
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
