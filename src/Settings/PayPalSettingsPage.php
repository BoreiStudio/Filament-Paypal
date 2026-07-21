<?php

namespace BoreiStudio\FilamentPayPal\Settings;

use BoreiStudio\FilamentPayPal\Clusters\PayPalConfigCluster;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class PayPalSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $cluster = PayPalConfigCluster::class;

    protected Width|string|null $maxContentWidth = Width::ScreenTwoExtraLarge;

    protected string $view = 'filament-paypal::settings-page';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        $role = config('filament-paypal.settings_role');

        if ($role && method_exists($user, 'hasRole') && $user->hasRole($role)) {
            return true;
        }

        if (! $role && method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
            return true;
        }

        return $user->can('viewAny', PaypalAccount::class);
    }

    public ?array $data = [];

    public function mount(): void
    {
        $account = PaypalAccount::whereNull('tenant_id')
            ->whereNull('tenant_type')
            ->first();

        $this->form->fill([
            'production_client_id' => $account?->production_client_id ?? '',
            'production_client_secret' => $account?->production_client_secret ?? '',
            'production_webhook_id' => $account?->production_webhook_id ?? '',
            'sandbox_client_id' => $account?->sandbox_client_id ?? '',
            'sandbox_client_secret' => $account?->sandbox_client_secret ?? '',
            'sandbox_webhook_id' => $account?->sandbox_webhook_id ?? '',
            'sandbox_mode' => $account?->sandbox_mode ?? true,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make(__('filament-paypal::messages.settings.title'))
                    ->description(__('filament-paypal::messages.settings.description'))
                    ->headerActions([
                        Action::make('toggle_sandbox')
                            ->label(fn () => $this->getModeLabel())
                            ->color(fn () => ($this->data['sandbox_mode'] ?? false) ? 'warning' : 'gray')
                            ->icon(fn () => ($this->data['sandbox_mode'] ?? false) ? 'heroicon-o-beaker' : 'heroicon-o-rocket-launch')
                            ->requiresConfirmation()
                            ->modalHeading(fn () => $this->getSwitchConfirmTitle())
                            ->modalDescription(fn () => $this->getSwitchConfirmBody())
                            ->modalSubmitActionLabel(__('filament-paypal::messages.settings.switch_submit'))
                            ->action(function () {
                                $this->data['sandbox_mode'] = ! ($this->data['sandbox_mode'] ?? false);
                                $this->persistSettings();
                                Notification::make()
                                    ->title($this->getSwitchedNotification())
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->footerActions([
                        Action::make('save')
                            ->label(__('filament-paypal::messages.settings.save'))
                            ->action('save')
                            ->color('primary'),
                    ])
                    ->schema([
                        Tabs::make()->tabs([
                            Tab::make(__('filament-paypal::messages.settings.production_tab'))
                                ->schema([
                                    Section::make()
                                        ->columns(2)
                                        ->schema([
                                            TextInput::make('production_client_id')
                                                ->label(__('filament-paypal::messages.settings.client_id'))
                                                ->maxLength(255),

                                            TextInput::make('production_client_secret')
                                                ->label(__('filament-paypal::messages.settings.client_secret'))
                                                ->password()
                                                ->revealable()
                                                ->maxLength(255),

                                            TextInput::make('production_webhook_id')
                                                ->label(__('filament-paypal::messages.settings.webhook_id'))
                                                ->helperText(__('filament-paypal::messages.settings.webhook_id_help'))
                                                ->maxLength(255),
                                        ]),
                                ]),

                            Tab::make(__('filament-paypal::messages.settings.sandbox_tab'))
                                ->schema([
                                    Section::make()
                                        ->columns(2)
                                        ->schema([
                                            TextInput::make('sandbox_client_id')
                                                ->label(__('filament-paypal::messages.settings.client_id'))
                                                ->maxLength(255),

                                            TextInput::make('sandbox_client_secret')
                                                ->label(__('filament-paypal::messages.settings.client_secret'))
                                                ->password()
                                                ->revealable()
                                                ->maxLength(255),

                                            TextInput::make('sandbox_webhook_id')
                                                ->label(__('filament-paypal::messages.settings.webhook_id'))
                                                ->helperText(__('filament-paypal::messages.settings.webhook_id_help'))
                                                ->maxLength(255),
                                        ]),
                                ]),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $this->persistSettings();

        Notification::make()
            ->title(__('filament-paypal::messages.settings.saved'))
            ->success()
            ->send();
    }

    private function persistSettings(): void
    {
        $state = $this->form->getState();

        $account = PaypalAccount::firstOrNew([
            'tenant_id' => null,
            'tenant_type' => null,
        ]);

        $account->production_client_id = $state['production_client_id'] ?? '';
        $account->production_client_secret = $state['production_client_secret'] ?? '';
        $account->production_webhook_id = $state['production_webhook_id'] ?? '';
        $account->sandbox_client_id = $state['sandbox_client_id'] ?? '';
        $account->sandbox_client_secret = $state['sandbox_client_secret'] ?? '';
        $account->sandbox_webhook_id = $state['sandbox_webhook_id'] ?? '';
        $account->sandbox_mode = $this->data['sandbox_mode'] ?? true;
        $account->status = 'connected';
        $account->last_verified_at = now();
        $account->save();
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-paypal::messages.settings.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-paypal::messages.settings.title');
    }

    private function getModeLabel(): string
    {
        $key = ($this->data['sandbox_mode'] ?? false)
            ? 'filament-paypal::messages.settings.sandbox_mode_label'
            : 'filament-paypal::messages.settings.production_mode_label';

        return __($key);
    }

    private function getSwitchConfirmTitle(): string
    {
        $mode = ($this->data['sandbox_mode'] ?? false)
            ? __('filament-paypal::messages.settings.production_tab')
            : __('filament-paypal::messages.settings.sandbox_tab');

        return __('filament-paypal::messages.settings.switch_confirm_title', ['mode' => $mode]);
    }

    private function getSwitchConfirmBody(): string
    {
        $mode = ($this->data['sandbox_mode'] ?? false)
            ? __('filament-paypal::messages.settings.production_tab')
            : __('filament-paypal::messages.settings.sandbox_tab');

        return __('filament-paypal::messages.settings.switch_confirm_body', ['mode' => $mode]);
    }

    private function getSwitchedNotification(): string
    {
        $mode = $this->data['sandbox_mode']
            ? __('filament-paypal::messages.settings.sandbox_tab')
            : __('filament-paypal::messages.settings.production_tab');

        return __('filament-paypal::messages.settings.switched', ['mode' => $mode]);
    }
}
