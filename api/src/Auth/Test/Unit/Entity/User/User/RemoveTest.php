<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\User;
use App\Auth\Test\Builder\UserBuilder;
use DateMalformedStringException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(User::class)]
final class RemoveTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    #[DoesNotPerformAssertions]
    public function user_can_be_removed(): void
    {
        $user = (new UserBuilder())->build();
        $user->remove();
    }

    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function we_cannot_remove_an_active_user(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $this->expectExceptionMessage('Unable to remove an active user.');
        $user->remove();
    }
}
