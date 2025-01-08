<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\SocialMediaIdentity;
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
final class AttachSocialMediaTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_can_attach_social_media(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $socialMedia = new SocialMediaIdentity('vk', '0000001');
        $user->attachSocialMedia($socialMedia);

        self::assertCount(1, $socialMedias = $user->socialMedias());
        self::assertEquals($socialMedia, $socialMedias[0] ?? null);
    }

    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_cannot_attach_social_media_twice(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $socialMedia = new SocialMediaIdentity('vk', '0000001');

        $user->attachSocialMedia($socialMedia);

        $this->expectExceptionMessage('Social media is already attached.');
        $user->attachSocialMedia($socialMedia);
    }
}
