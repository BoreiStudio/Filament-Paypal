<?php

namespace BoreiStudio\FilamentPayPal\Features\Refunds\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RefundStatus: string implements HasColor, HasIcon, HasLabel
{
    case Completed = 'COMPLETED';
    case Pending = 'PENDING';
    case Failed = 'FAILED';
    case Cancelled = 'CANCELLED';

    public function getLabel(): string
    {
        return match ($this) {
            self::Completed => 'Completed',
            self::Pending => 'Pending',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Completed => 'success',
            self::Pending => 'warning',
            self::Failed => 'danger',
            self::Cancelled => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Completed => 'heroicon-o-check-circle',
            self::Pending => 'heroicon-o-clock',
            self::Failed => 'heroicon-o-exclamation-circle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }
}
