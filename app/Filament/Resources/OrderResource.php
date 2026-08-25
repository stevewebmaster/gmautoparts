<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getNavigationBadge(): ?string
    {
        $toPack = Order::where('status', OrderStatus::Paid)->count();

        return $toPack > 0 ? (string) $toPack : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Order')->schema([
                Forms\Components\TextInput::make('reference')->disabled(),
                Forms\Components\Select::make('status')
                    ->options(OrderStatus::options())
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('subtotal')->prefix('$')->disabled(),
                Forms\Components\TextInput::make('shipping')->prefix('$')->disabled(),
                Forms\Components\TextInput::make('total')->prefix('$')->disabled(),
                Forms\Components\TextInput::make('stripe_payment_intent')
                    ->label('Stripe payment')
                    ->disabled()
                    ->helperText('Look this up in the Stripe dashboard to refund.'),
            ])->columns(3),
            Forms\Components\Section::make('Customer')->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\TextInput::make('phone'),
            ])->columns(3),
            Forms\Components\Section::make('Delivery')->schema([
                Forms\Components\TextInput::make('fulfilment')->disabled(),
                Forms\Components\TextInput::make('address_line1')->label('Address'),
                Forms\Components\TextInput::make('address_line2')->label('Address line 2'),
                Forms\Components\TextInput::make('suburb'),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('postcode'),
                Forms\Components\TextInput::make('region')->disabled(),
                Forms\Components\Toggle::make('is_rural')->label('Rural delivery'),
                Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
            ])->columns(3),
            Forms\Components\Section::make('Items')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('title')->disabled(),
                        Forms\Components\TextInput::make('price')->prefix('$')->disabled(),
                        Forms\Components\TextInput::make('shipping_band')->disabled(),
                    ])
                    ->columns(3)
                    ->disabled()
                    ->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('name')->label('Customer')->searchable(),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Parts'),
                Tables\Columns\TextColumn::make('total')->money('NZD')->sortable(),
                Tables\Columns\TextColumn::make('fulfilment')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'pickup' ? 'Collecting' : 'Deliver')
                    ->color(fn (string $state) => $state === 'pickup' ? 'gray' : 'info'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state) => $state->label())
                    ->color(fn (OrderStatus $state) => $state->color())
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')->dateTime('d M Y H:i')->placeholder('—')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(OrderStatus::options())->multiple(),
                Tables\Filters\SelectFilter::make('fulfilment')->options([
                    'delivery' => 'Deliver',
                    'pickup' => 'Collecting',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('dispatched')
                    ->label('Dispatched')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->status === OrderStatus::Paid && ! $record->isPickup())
                    ->action(fn (Order $record) => $record->update([
                        'status' => OrderStatus::Dispatched,
                        'dispatched_at' => now(),
                    ])),
                Tables\Actions\Action::make('collected')
                    ->label('Collected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->status === OrderStatus::Paid && $record->isPickup())
                    ->action(fn (Order $record) => $record->update(['status' => OrderStatus::Collected])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'name', 'email'];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
