<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'auth_users')]
#[ORM\HasLifecycleCallbacks]
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
    #[ORM\Embedded(class: Token::class)]
    private ?Token $joinConfirmationToken = null;
    #[ORM\Embedded(class: Token::class)]
    private ?Token $passwordResetToken = null;
    #[ORM\Embedded(class: Token::class)]
    private ?Token $newEmailToken = null;
    #[ORM\Column(type: StatusType::NAME, length: 16)]
    private Status $status;
    #[ORM\Column(type: RoleType::NAME, length: 16)]
    private Role $role;
    /** @var Collection<array-key, UserSocialMedia>  */
    #[ORM\OneToMany(targetEntity: UserSocialMedia::class, mappedBy: 'user', cascade: ['all'], orphanRemoval: true)]
    private Collection $socialMedias;

    private function __construct(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Status $status = Status::Wait,
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = $status;
        $this->role = Role::User;
        $this->socialMedias = new ArrayCollection();
    }

    public static function requestJoinByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        $user = new self(
            id: $id,
            date: $date,
            email: $email,
        );

        $user->passwordHash = $passwordHash;
        $user->joinConfirmationToken = $token;

        return $user;
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

        $user->socialMedias->add(new UserSocialMedia($user, $identity));

        return $user;
    }

    public function attachSocialMedia(SocialMedia $identity): void
    {
        foreach ($this->socialMedias as $existing) {
            if ($existing->socialMedia()->isEqualTo($identity)) {
                throw new DomainException('Social media is already attached.');
            }
        }

        $this->socialMedias->add(new UserSocialMedia($this, $identity));
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
        return array_map(static fn (UserSocialMedia $socialMedia) => $socialMedia->socialMedia(), $this->socialMedias->toArray());
    }

    #[ORM\PostLoad()]
    public function checkEmbeds(): void
    {
        if ($this->joinConfirmationToken && $this->joinConfirmationToken->isEmpty()) {
            $this->joinConfirmationToken = null;
        }
        if ($this->passwordResetToken && $this->passwordResetToken->isEmpty()) {
            $this->passwordResetToken = null;
        }
        if ($this->newEmailToken && $this->newEmailToken->isEmpty()) {
            $this->newEmailToken = null;
        }
    }
}
