<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case Created = 'CREATED';
    case Approved = 'APPROVED';
    case Completed = 'COMPLETED';
    case Voided = 'VOIDED';

    public function getLabel(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::Approved => 'Approved',
            self::Completed => 'Completed',
            self::Voided => 'Voided',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Created => 'info',
            self::Approved => 'warning',
            self::Completed => 'success',
            self::Voided => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Created => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-check-badge',
            self::Completed => 'heroicon-o-check-circle',
            self::Voided => 'heroicon-o-x-circle',
        };
    }
}
