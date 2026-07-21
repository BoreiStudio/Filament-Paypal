<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources;

use BoreiStudio\FilamentPayPal\Clusters\PayPalCluster;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Actions\CreatePlanAction;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Plan;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Product;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources\Pages\ManagePlans;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $cluster = PayPalCluster::class;

    protected static ?string $slug = 'subscription-plans';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('product_id')
                    ->label(__('filament-paypal::messages.plans.product'))
                    ->options(Product::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label(__('filament-paypal::messages.plans.name'))
                    ->required(),
                TextInput::make('description')
                    ->label(__('filament-paypal::messages.plans.description')),
                TextInput::make('amount')
                    ->label(__('filament-paypal::messages.plans.amount'))
                    ->numeric()
                    ->required()
                    ->prefix('USD'),
                Select::make('billing_frequency')
                    ->options([
                        'DAY' => 'Daily',
                        'WEEK' => 'Weekly',
                        'MONTH' => 'Monthly',
                        'YEAR' => 'Yearly',
                    ])
                    ->default('MONTH'),
                TextInput::make('billing_cycles')
                    ->label(__('filament-paypal::messages.plans.billing_cycles'))
                    ->numeric()
                    ->default(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-paypal::messages.plans.name'))
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label(__('filament-paypal::messages.plans.product')),
                TextColumn::make('amount')
                    ->label(__('filament-paypal::messages.plans.amount'))
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('billing_frequency')
                    ->label(__('filament-paypal::messages.plans.billing_frequency'))
                    ->badge(),
                TextColumn::make('billing_cycles')
                    ->label(__('filament-paypal::messages.plans.billing_cycles')),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('subscriptions_count')
                    ->label(__('filament-paypal::messages.plans.subscriptions_count'))
                    ->counts('subscriptions'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                Action::make('create')
                    ->label(__('filament-paypal::messages.plans.create'))
                    ->icon(Heroicon::Plus)
                    ->slideOver()
                    ->form([
                        Select::make('product_id')
                            ->label(__('filament-paypal::messages.plans.product'))
                            ->options(Product::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label(__('filament-paypal::messages.plans.name'))
                            ->required(),
                        TextInput::make('description')
                            ->label(__('filament-paypal::messages.plans.description')),
                        TextInput::make('amount')
                            ->label(__('filament-paypal::messages.plans.amount'))
                            ->numeric()
                            ->required()
                            ->prefix('USD'),
                        Select::make('billing_frequency')
                            ->options([
                                'DAY' => 'Daily',
                                'WEEK' => 'Weekly',
                                'MONTH' => 'Monthly',
                                'YEAR' => 'Yearly',
                            ])
                            ->default('MONTH'),
                        TextInput::make('billing_cycles')
                            ->label(__('filament-paypal::messages.plans.billing_cycles'))
                            ->numeric()
                            ->default(12),
                    ])
                    ->action(function (array $data) {
                        $product = Product::findOrFail($data['product_id']);
                        app(CreatePlanAction::class)->execute($product, $data);
                        Notification::make()->success()->title(__('filament-paypal::messages.plans.created'))->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePlans::route('/'),
        ];
    }
}
