<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Role;
use App\Auth\Entity\User\SocialMedia;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(User::class)]
final class JoinBySocialMediaTest extends TestCase
{
    #[Test]
    public function it_can_successfully_join_by_social_media(): void
    {
        $user = User::joinBySocialMedia(
            $id = Id::next(),
            $date = new DateTimeImmutable(),
            $email = new Email('email@app.test'),
            $socialMedia = new SocialMedia('vk', '0000001')
        );

        self::assertEquals($id, $user->id());
        self::assertEquals($date, $user->date());
        self::assertEquals($email, $user->email());

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertEquals(Role::User, $user->role());

        self::assertCount(1, $networks = $user->socialMedias());
        self::assertEquals($socialMedia, $networks[0] ?? null);
    }
}
