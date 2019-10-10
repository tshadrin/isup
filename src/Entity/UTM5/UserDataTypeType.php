<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class UserDataTypeType extends StringType
{
    public const NAME = 'user_data_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof UserDataType ? $value->getName() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new UserDataType($value) : null;
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