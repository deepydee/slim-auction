<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangPassword;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\PasswordHasher;
use App\Flusher;

final class Handler
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private Flusher $flusher,
    ) {
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $user->changePassword(
            $command->currentPassword,
            $command->newPassword,
            $this->hasher,
        );

        $this->flusher->flush();
    }
}
