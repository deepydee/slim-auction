<?php

declare(strict_types=1);

namespace App\Auth\Command\AttachSocialMedia;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\SocialMedia;
use App\Auth\Entity\User\UserRepository;
use App\Flusher;
use DomainException;

final class Handler
{
    public function __construct(
        private UserRepository $users,
        private Flusher $flusher,
    ) {
    }

    public function handle(Command $command): void
    {
        $identity = new SocialMedia($command->socialMedia, $command->identity);

        if ($this->users->hasBySocialMedia($identity)) {
            throw new DomainException('User with this social media already exists.');
        }

        $user = $this->users->get(new Id($command->id));
        $user->attachSocialMedia($identity);

        $this->flusher->flush();
    }
}
