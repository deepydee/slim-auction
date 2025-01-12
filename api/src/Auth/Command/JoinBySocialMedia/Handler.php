<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinBySocialMedia;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\SocialMedia;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use App\Flusher;
use DateTimeImmutable;
use DomainException;

final readonly class Handler
{
    public function __construct(
        private UserRepository $users,
        private Flusher $flusher,
    ) {
    }

    public function handle(Command $command): void
    {
        $identity = new SocialMedia($command->socialMedia, $command->identity);
        $email = new Email($command->email);

        if ($this->users->hasBySocialMedia($identity)) {
            throw new DomainException('User with this social media already exists.');
        }

        if ($this->users->hasByEmail($email)) {
            throw new DomainException('User with this email already exists.');
        }

        $user = User::joinBySocialMedia(
            Id::next(),
            new DateTimeImmutable(),
            $email,
            $identity
        );

        $this->users->add($user);

        $this->flusher->flush();
    }
}
