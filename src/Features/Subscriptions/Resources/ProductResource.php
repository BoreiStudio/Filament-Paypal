<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources;

use BoreiStudio\FilamentPayPal\Clusters\PayPalCluster;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Actions\CreateProductAction;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Product;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources\Pages\ManageProducts;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $cluster = PayPalCluster::class;

    protected static ?string $slug = 'subscription-products';

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::Cube;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('account_id')
                    ->label(__('filament-paypal::messages.products.account'))
                    ->options(PaypalAccount::where('status', 'connected')->pluck('production_client_id', 'id'))
                    ->required(),
                TextInput::make('name')
                    ->label(__('filament-paypal::messages.products.name'))
                    ->required()
                    ->maxLength(127),
                TextInput::make('description')
                    ->label(__('filament-paypal::messages.products.description'))
                    ->maxLength(255),
                Select::make('type')
                    ->options([
                        'SERVICE' => 'Service',
                        'PHYSICAL' => 'Physical',
                        'DIGITAL' => 'Digital',
                    ])
                    ->default('SERVICE'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-paypal::messages.products.name'))
                    ->searchable(),
                TextColumn::make('paypal_product_id')
                    ->label(__('filament-paypal::messages.products.paypal_product_id'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')
                    ->label(__('filament-paypal::messages.products.type'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('filament-paypal::messages.products.status'))
                    ->badge(),
                TextColumn::make('plans_count')
                    ->label(__('filament-paypal::messages.products.plans_count'))
                    ->counts('plans'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                Action::make('create')
                    ->label(__('filament-paypal::messages.products.create'))
                    ->icon(Heroicon::Plus)
                    ->slideOver()
                    ->form([
                        Select::make('account_id')
                            ->label(__('filament-paypal::messages.products.account'))
                            ->options(PaypalAccount::where('status', 'connected')->pluck('production_client_id', 'id'))
                            ->required(),
                        TextInput::make('name')
                            ->label(__('filament-paypal::messages.products.name'))
                            ->required()
                            ->maxLength(127),
                        TextInput::make('description')
                            ->label(__('filament-paypal::messages.products.description'))
                            ->maxLength(255),
                        Select::make('type')
                            ->options([
                                'SERVICE' => 'Service',
                                'PHYSICAL' => 'Physical',
                                'DIGITAL' => 'Digital',
                            ])
                            ->default('SERVICE'),
                    ])
                    ->action(function (array $data) {
                        app(CreateProductAction::class)->execute($data);
                        Notification::make()->success()->title(__('filament-paypal::messages.products.created'))->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProducts::route('/'),
        ];
    }
}
