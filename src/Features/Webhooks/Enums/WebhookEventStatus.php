<?php

namespace BoreiStudio\FilamentPayPal\Features\Webhooks\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum WebhookEventStatus: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Processed = 'processed';
    case Failed = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Processed => 'Processed',
            self::Failed => 'Failed',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processed => 'success',
            self::Failed => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Processed => 'heroicon-o-check-circle',
            self::Failed => 'heroicon-o-exclamation-circle',
        };
    }
}
