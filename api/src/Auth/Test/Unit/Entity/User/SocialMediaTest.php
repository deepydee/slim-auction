<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\SocialMedia;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SocialMedia::class)]
final class SocialMediaTest extends TestCase
{
    #[Test]
    public function it_can_successfully_be_created(): void
    {
        $socialMedia = new SocialMedia($name = 'google', $identity = 'google-1');

        self::assertEquals($name, $socialMedia->name());
        self::assertEquals($identity, $socialMedia->identity());
    }

    #[Test]
    public function it_cannot_be_created_with_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SocialMedia($name = '', $identity = 'google-1');
    }

    #[Test]
    public function test_empty_identity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SocialMedia($name = 'google', $identity = '');
    }

    #[Test]
    public function test_equal(): void
    {
        $network = new SocialMedia($name = 'google', $identity = 'google-1');

        self::assertTrue($network->isEqualTo(new SocialMedia($name, 'google-1')));
        self::assertFalse($network->isEqualTo(new SocialMedia($name, 'google-2')));
        self::assertFalse($network->isEqualTo(new SocialMedia('vk', 'vk-1')));
    }
}
