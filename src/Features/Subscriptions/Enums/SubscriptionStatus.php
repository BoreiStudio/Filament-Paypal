<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SubscriptionStatus: string implements HasColor, HasIcon, HasLabel
{
    case ApprovalPending = 'APPROVAL_PENDING';
    case Approved = 'APPROVED';
    case Active = 'ACTIVE';
    case Suspended = 'SUSPENDED';
    case Cancelled = 'CANCELLED';
    case Expired = 'EXPIRED';

    public function getLabel(): string
    {
        return match ($this) {
            self::ApprovalPending => 'Approval Pending',
            self::Approved => 'Approved',
            self::Active => 'Active',
            self::Suspended => 'Suspended',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Expired',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ApprovalPending => 'warning',
            self::Approved => 'info',
            self::Active => 'success',
            self::Suspended => 'warning',
            self::Cancelled => 'danger',
            self::Expired => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ApprovalPending => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-check-badge',
            self::Active => 'heroicon-o-check-circle',
            self::Suspended => 'heroicon-o-pause-circle',
            self::Cancelled => 'heroicon-o-x-circle',
            self::Expired => 'heroicon-o-clock',
        };
    }
}
