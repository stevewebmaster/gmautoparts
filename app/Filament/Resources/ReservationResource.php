<?php

namespace App\Filament\Resources;

use App\Enums\ReservationStatus;
use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getNavigationBadge(): ?string
    {
        $holding = Reservation::holding()->count();

        return $holding > 0 ? (string) $holding : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Reservation')->schema([
                    Forms\Components\TextInput::make('reference')->disabled(),
                    Forms\Components\Select::make('status')
                        ->options(ReservationStatus::options())
                        ->required()
                        ->native(false)
                        ->helperText('Changing this here does not move the part. Use the Collected / Cancelled buttons on the list, or the Parts Loader app.'),
                    Forms\Components\TextInput::make('part_title')->disabled(),
                    Forms\Components\TextInput::make('part_price')->prefix('$')->disabled(),
                    Forms\Components\DateTimePicker::make('expires_at')->label('Collect by'),
                ])->columns(2),
                Forms\Components\Section::make('Customer')->schema([
                    Forms\Components\TextInput::make('name'),
                    Forms\Components\TextInput::make('email')->email(),
                    Forms\Components\TextInput::make('phone'),
                    Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('part_title')->label('Part')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('name')->label('Customer')->searchable(),
                Tables\Columns\TextColumn::make('phone')->toggleable(),
                Tables\Columns\TextColumn::make('part_price')->money('NZD')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ReservationStatus $state) => $state->label())
                    ->color(fn (ReservationStatus $state) => $state->color())
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Collect by')
                    ->dateTime('d M Y')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ReservationStatus::options())
                    ->multiple(),
            ])
            ->actions([
                // Routed through the model so the part's status follows along.
                Tables\Actions\Action::make('collected')
                    ->label('Collected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Marks the reservation collected and the part Sold.')
                    ->visible(fn (Reservation $record) => $record->isHolding())
                    ->action(fn (Reservation $record) => $record->markCollected()),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Cancels the reservation and puts the part back on sale.')
                    ->visible(fn (Reservation $record) => $record->isHolding())
                    ->action(fn (Reservation $record) => $record->cancel()),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'part_title', 'name', 'email'];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
