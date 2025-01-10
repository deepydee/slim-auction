<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Role;
use App\Auth\Entity\User\User;
use App\Auth\Test\Builder\UserBuilder;
use DateMalformedStringException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(User::class)]
final class ChangeRoleTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_role_can_be_changed(): void
    {
        $user = (new UserBuilder())
            ->build();

        $user->changeRole($role = Role::Admin);

        self::assertEquals($role, $user->role());
    }
}
