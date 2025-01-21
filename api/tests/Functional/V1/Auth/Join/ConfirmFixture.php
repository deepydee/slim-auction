<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth\Join;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateMalformedStringException;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class ConfirmFixture extends AbstractFixture
{
    public const string VALID = '00000000-0000-0000-0000-000000000001';
    public const string EXPIRED = '00000000-0000-0000-0000-000000000002';

    /**
     * @throws DateMalformedStringException
     */
    public function load(ObjectManager $manager): void
    {
        // Valid

        $user = User::requestJoinByEmail(
            Id::next(),
            $date = new DateTimeImmutable(),
            new Email('valid@app.test'),
            'password-hash',
            new Token($value = self::VALID, $date->modify('+1 hour'))
        );

        $manager->persist($user);

        // Expired

        $user = User::requestJoinByEmail(
            Id::next(),
            $date = new DateTimeImmutable(),
            new Email('expired@app.test'),
            'password-hash',
            new Token($value = self::EXPIRED, $date->modify('-2 hours'))
        );

        $manager->persist($user);

        $manager->flush();
    }
}
