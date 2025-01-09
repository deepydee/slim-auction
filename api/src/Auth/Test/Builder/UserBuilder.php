<?php

declare(strict_types=1);

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\SocialMediaIdentity;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateMalformedStringException;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

final class UserBuilder
{
    private Id $id;
    private Email $email;
    private string $hash;
    private DateTimeImmutable $date;
    private Token $joinConfirmToken;
    private bool $active = false;
    private ?SocialMediaIdentity $socialMediaIdentity = null;

    /**
     * @throws DateMalformedStringException
     */
    public function __construct()
    {
        $this->id = Id::next();
        $this->email = new Email('mail@example.com');
        $this->hash = 'hash';
        $this->date = new DateTimeImmutable();
        $this->joinConfirmToken = new Token(Uuid::uuid4()->toString(), $this->date->modify('+1 day'));
    }

    public function withJoinConfirmationToken(Token $token): self
    {
        $clone = clone $this;
        $clone->joinConfirmToken = $token;

        return $clone;
    }

    public function viaNetwork(?SocialMediaIdentity $identity = null): self
    {
        $clone = clone $this;
        $clone->socialMediaIdentity = $identity ?? new SocialMediaIdentity('vk', '0000001');

        return $clone;
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;

        return $clone;
    }

    /**
     * @throws DateMalformedStringException
     */
    public function build(): User
    {
        if (! is_null($this->socialMediaIdentity)) {
            return User::joinBySocialMedia(
                $this->id,
                $this->date,
                $this->email,
                $this->socialMediaIdentity,
            );
        }

        $user = User::requestJoinByEmail(
            $this->id,
            $this->date,
            $this->email,
            $this->hash,
            $this->joinConfirmToken
        );

        if ($this->active) {
            $user->confirmJoin(
                $this->joinConfirmToken->value(),
                $this->joinConfirmToken->expiresAt()->modify('-1 day')
            );
        }

        return $user;
    }
}
