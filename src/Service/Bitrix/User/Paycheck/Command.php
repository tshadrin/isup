<?php
declare(strict_types = 1);


namespace App\Service\Bitrix\User\Paycheck;


use Webmozart\Assert\Assert;

class Command
{
    /**
     * @var array
     * ["crm","CCrmDocumentDeal","DEAL_<NUM>"]
     */
    public $document;

    public function __construct(array $document)
    {
        Assert::nullOrCount($document, 3);
        $this->document = $document;
    }
}