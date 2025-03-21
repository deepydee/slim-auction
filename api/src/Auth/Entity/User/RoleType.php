<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Override;

final class RoleType extends StringType
{
    public const string NAME = 'auth_user_role';

    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Role ? $value->value : $value;
    }

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        return ! empty($value) ? Role::tryFrom((string) $value) : null;
    }
}
