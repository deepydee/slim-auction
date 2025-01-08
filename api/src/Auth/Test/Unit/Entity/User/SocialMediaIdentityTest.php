<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\SocialMediaIdentity;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SocialMediaIdentity::class)]
final class SocialMediaIdentityTest extends TestCase
{
    #[Test]
    public function it_can_successfully_be_created(): void
    {
        $socialMedia = new SocialMediaIdentity($name = 'google', $identity = 'google-1');

        self::assertEquals($name, $socialMedia->name());
        self::assertEquals($identity, $socialMedia->identity());
    }

    #[Test]
    public function it_cannot_be_created_with_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SocialMediaIdentity($name = '', $identity = 'google-1');
    }

    #[Test]
    public function test_empty_identity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SocialMediaIdentity($name = 'google', $identity = '');
    }

    #[Test]
    public function test_equal(): void
    {
        $network = new SocialMediaIdentity($name = 'google', $identity = 'google-1');

        self::assertTrue($network->isEqualTo(new SocialMediaIdentity($name, 'google-1')));
        self::assertFalse($network->isEqualTo(new SocialMediaIdentity($name, 'google-2')));
        self::assertFalse($network->isEqualTo(new SocialMediaIdentity('vk', 'vk-1')));
    }
}
