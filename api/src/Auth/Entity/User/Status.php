<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

enum Status: string
{
    case Active = 'active';
    case Wait = 'wait';
    
    public function isActive(): bool
    {
        return $this === self::Active;
    }
    
    public function isWait(): bool
    {
        return $this === self::Wait;
    }
}
