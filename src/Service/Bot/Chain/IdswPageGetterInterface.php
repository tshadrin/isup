<?php
declare(strict_types=1);

namespace App\Service\Bot\Chain;


interface IdswPageGetterInterface
{
    public function getIdswPage(int $id);
}