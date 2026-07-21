<?php

namespace BoreiStudio\FilamentPayPal\Features\Payments\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasColor, HasIcon, HasLabel
{
    case Completed = 'COMPLETED';
    case Pending = 'PENDING';
    case Refunded = 'REFUNDED';
    case PartiallyRefunded = 'PARTIALLY_REFUNDED';
    case Declined = 'DECLINED';
    case Failed = 'FAILED';

    public function getLabel(): string
    {
        return match ($this) {
            self::Completed => 'Completed',
            self::Pending => 'Pending',
            self::Refunded => 'Refunded',
            self::PartiallyRefunded => 'Partially Refunded',
            self::Declined => 'Declined',
            self::Failed => 'Failed',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Completed => 'success',
            self::Pending => 'warning',
            self::Refunded, self::PartiallyRefunded => 'danger',
            self::Declined, self::Failed => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Completed => 'heroicon-o-check-circle',
            self::Pending => 'heroicon-o-clock',
            self::Refunded => 'heroicon-o-arrow-uturn-left',
            self::PartiallyRefunded => 'heroicon-o-arrow-uturn-left',
            self::Declined => 'heroicon-o-x-circle',
            self::Failed => 'heroicon-o-exclamation-circle',
        };
    }
}
