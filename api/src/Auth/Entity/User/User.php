<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;
use ArrayObject;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: IdType::NAME)]
final class User
{
    #[ORM\Column(type: IdType::NAME)]
    #[ORM\Id]
    private readonly Id $id;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $date;
    #[ORM\Column(type: EmailType::NAME, unique: true)]
    private Email $email;
    #[ORM\Column(type: EmailType::NAME, nullable: true)]
    private ?Email $newEmail = null;
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $passwordHash = null;
    private ?Token $joinConfirmationToken = null;
    private ?Token $passwordResetToken = null;
    #[ORM\Column(type: StatusType::NAME, length: 16)]
    private Status $status;
    #[ORM\Column(type: RoleType::NAME, length: 16)]
    private Role $role;
    private ArrayObject $socialMedias;

    private ?Token $newEmailToken = null;

    private function __construct(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        ?string $passwordHash = null,
        ?Token $joinConfirmationToken = null,
        Status $status = Status::Wait,
        Role $role = Role::User,
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->joinConfirmationToken = $joinConfirmationToken;
        $this->status = $status;
        $this->role = $role;
        $this->socialMedias = new ArrayObject();
    }

    public static function requestJoinByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        return new self(
            id: $id,
            date: $date,
            email: $email,
            passwordHash: $passwordHash,
            joinConfirmationToken: $token,
        );
    }

    public function requestPasswordReset(Token $token, DateTimeImmutable $date): void
    {
        if (! $this->isActive()) {
            throw new DomainException('User is not active.');
        }
        if (! is_null($this->passwordResetToken) && ! $this->passwordResetToken->isExpiredTo($date)) {
            throw new DomainException('Resetting is already requested.');
        }

        $this->passwordResetToken = $token;
    }

    public function resetPassword(string $token, DateTimeImmutable $date, string $hash): void
    {
        if (is_null($this->passwordResetToken)) {
            throw new DomainException('Resetting is not requested.');
        }

        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if (is_null($this->passwordHash)) {
            throw new DomainException('User does not have an old password.');
        }
        if (! $hasher->validate($current, $this->passwordHash)) {
            throw new DomainException('Incorrect current password.');
        }

        $this->passwordHash = $hasher->hash($new);
    }

    public function requestEmailChanging(Token $token, DateTimeImmutable $date, Email $email): void
    {
        if (! $this->isActive()) {
            throw new DomainException('User is not active.');
        }
        if ($this->email->isEqualTo($email)) {
            throw new DomainException('Email is already same.');
        }
        if (! is_null($this->newEmailToken) && ! $this->newEmailToken->isExpiredTo($date)) {
            throw new DomainException('Changing is already requested.');
        }

        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    public function confirmEmailChanging(string $token, DateTimeImmutable $date): void
    {
        if (is_null($this->newEmail) || is_null($this->newEmailToken)) {
            throw new DomainException('Changing is not requested.');
        }

        $this->newEmailToken->validate($token, $date);
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    public static function joinBySocialMedia(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        SocialMedia $identity
    ): self {
        $user = new self(
            id: $id,
            date: $date,
            email: $email,
            status: Status::Active,
        );

        $user->socialMedias->append($identity);

        return $user;
    }

    public function attachSocialMedia(SocialMedia $identity): void
    {
        /** @var SocialMedia $existing */
        foreach ($this->socialMedias as $existing) {
            if ($existing->isEqualTo($identity)) {
                throw new DomainException('Social media is already attached.');
            }
        }

        $this->socialMedias->append($identity);
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function changeRole(Role $role): void
    {
        $this->role = $role;
    }

    public function remove(): void
    {
        if (! $this->isWait()) {
            throw new DomainException('Unable to remove an active user.');
        }
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function newEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function newEmailToken(): ?Token
    {
        return $this->newEmailToken;
    }

    public function passwordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function passwordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    public function joinConfirmationToken(): ?Token
    {
        return $this->joinConfirmationToken;
    }

    public function confirmJoin(string $token, DateTimeImmutable $date): void
    {
        if (is_null($this->joinConfirmationToken)) {
            throw new DomainException('Confirmation is not required.');
        }

        $this->joinConfirmationToken->validate($token, $date);
        $this->status = Status::Active;
        $this->joinConfirmationToken = null;
    }


    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    /** @return list<SocialMedia> */
    public function socialMedias(): array
    {
        /** @var list<SocialMedia> */
        return $this->socialMedias->getArrayCopy();
    }
}
