<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class TypicalCallGroupType extends StringType
{
    public const NAME = 'typical_call_group';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof TypicalCallGroup ? $value->getName() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new TypicalCallGroup($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
    {
        return true;
    }
}