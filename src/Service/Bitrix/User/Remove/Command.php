<?php
declare(strict_types=1);

namespace App\Service\Bitrix\User\Remove;

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
        Assert::count($document, 3, 'Parameter document_id not found');
        $this->document = $document;
    }
}