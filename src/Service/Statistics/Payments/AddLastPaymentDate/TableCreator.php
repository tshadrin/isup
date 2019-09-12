<?php
declare(strict_types=1);

namespace App\Service\Statistics\Payments\AddLastPaymentDate;


use App\Entity\Statistics\LastPayment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class TableCreator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createLastPaymentDateTable(Command $command): void
    {
        $metadata = $this->entityManager->getClassMetadata(LastPayment::class);
        $metadata->setPrimaryTable(array('name' => $metadata->getTableName() . "_{$command->month}_{$command->year}"));

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema(array($metadata));
    }
}
