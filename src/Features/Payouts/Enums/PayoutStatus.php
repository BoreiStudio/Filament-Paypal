<?php

namespace BoreiStudio\FilamentPayPal\Features\Payouts\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PayoutStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'PENDING';
    case Success = 'SUCCESS';
    case Denied = 'DENIED';
    case Cancelled = 'CANCELLED';
    case Failed = 'FAILED';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Success => 'Success',
            self::Denied => 'Denied',
            self::Cancelled => 'Cancelled',
            self::Failed => 'Failed',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Success => 'success',
            self::Denied => 'danger',
            self::Cancelled => 'gray',
            self::Failed => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Success => 'heroicon-o-check-circle',
            self::Denied => 'heroicon-o-x-circle',
            self::Cancelled => 'heroicon-o-x-circle',
            self::Failed => 'heroicon-o-exclamation-circle',
        };
    }
}
