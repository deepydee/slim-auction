<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'auth_user_social_media')]
#[ORM\UniqueConstraint(columns: ['social_media_name', 'social_media_identity'])]
final readonly class UserSocialMedia
{
    #[ORM\Column(type: Types::GUID)]
    #[ORM\Id]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'socialMedias')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Embedded(class: SocialMedia::class)]
    private SocialMedia $socialMedia;

    public function __construct(User $user, SocialMedia $socialMedia)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->socialMedia = $socialMedia;
    }

    public function socialMedia(): SocialMedia
    {
        return $this->socialMedia;
    }
}
