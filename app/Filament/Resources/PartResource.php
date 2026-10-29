<?php

namespace App\Filament\Resources;

use App\Enums\FeedStatus;
use App\Enums\PartStatus;
use App\Enums\ShippingBand;
use App\Filament\Resources\PartResource\Pages;
use App\Models\Part;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PartResource extends Resource
{
    protected static ?string $model = Part::class;

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Part details')->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('part_category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('part_subcategory_id', null)),
                    Forms\Components\Select::make('part_subcategory_id')
                        ->relationship(
                            'subcategory',
                            'name',
                            modifyQueryUsing: fn ($query, $get) => $query->where('part_category_id', $get('part_category_id') ?: 0)
                        )
                        ->searchable()
                        ->preload(),
                    Forms\Components\Textarea::make('description')->rows(4),
                    Forms\Components\TextInput::make('stock_number'),
                    Forms\Components\TextInput::make('condition'),
                    Forms\Components\TextInput::make('price')
                        ->label('Price incl. GST')
                        ->numeric()
                        ->prefix('$'),
                ])->columns(2),
                Forms\Components\Section::make('Stock & availability')->schema([
                    Forms\Components\Select::make('status')
                        ->options(PartStatus::options())
                        ->default(PartStatus::Available->value)
                        ->required()
                        ->native(false)
                        ->helperText('Sold parts keep their page but drop out of the parts listing. Withdrawn parts disappear from the site entirely.'),
                    Forms\Components\TextInput::make('quantity')
                        ->numeric()
                        ->minValue(0)
                        ->default(1)
                        ->required(),
                    Forms\Components\Select::make('shipping_band')
                        ->label('Freight size')
                        ->options(ShippingBand::optionsWithRates())
                        ->native(false)
                        ->helperText('The NZ Couriers satchel or box it goes in — pick it the same way as in eDespatchIT. Sets the freight price online. Leave empty and the part is NOT sold online — it falls back to reserve/enquire and is left out of the Google feed.')
                        ->columnSpanFull(),
                ])->columns(2),
                Forms\Components\Section::make('Vehicle compatibility')->schema([
                    Forms\Components\TextInput::make('make'),
                    Forms\Components\TextInput::make('model'),
                    Forms\Components\TextInput::make('year'),
                    Forms\Components\Select::make('vehicle_id')
                        ->relationship('vehicle', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                        ->searchable()
                        ->preload(),
                ])->columns(2)->collapsed(),
                Forms\Components\Section::make('Media & visibility')->schema([
                    Forms\Components\FileUpload::make('images')
                        ->image()
                        ->multiple()
                        ->directory('parts')
                        ->disk('public')
                        ->reorderable()
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_visible')->default(true),
                    Forms\Components\Toggle::make('is_featured')->default(false),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->stacked()
                    ->limit(1)
                    ->circular()
                    ->getStateUsing(fn ($record) => is_array($record->images) && count($record->images) ? $record->images[0] : null),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\TextColumn::make('stock_number'),
                Tables\Columns\TextColumn::make('price')->money('NZD')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (PartStatus $state) => $state->label())
                    ->color(fn (PartStatus $state) => $state->color())
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('shipping_band')
                    ->label('Freight')
                    ->badge()
                    ->formatStateUsing(fn (?ShippingBand $state) => $state?->shortLabel() ?? 'Not set')
                    ->color(fn (?ShippingBand $state) => $state?->color() ?? 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sold_at')
                    ->dateTime('d M Y')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_visible')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                // Not sortable: it is derived from four columns, not stored.
                Tables\Columns\TextColumn::make('feed_status')
                    ->label('Google')
                    ->badge()
                    ->getStateUsing(fn (Part $record) => $record->feedStatus())
                    ->formatStateUsing(fn (FeedStatus $state) => $state->label())
                    ->color(fn (FeedStatus $state) => $state->color())
                    ->tooltip(fn (FeedStatus $state) => $state->description()),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(PartStatus::options())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('shipping_band')
                    ->label('Freight size')
                    ->options(['__none' => 'Not set — not sold online'] + ShippingBand::options())
                    ->query(function ($query, array $data) {
                        if (($data['value'] ?? null) === '__none') {
                            return $query->whereNull('shipping_band');
                        }

                        return $data['value'] ? $query->where('shipping_band', $data['value']) : $query;
                    }),
                // Shares FeedStatus::constrain() with the dashboard widget, so
                // a count there and the rows here can never disagree.
                Tables\Filters\SelectFilter::make('feed')
                    ->label('Google feed')
                    ->options(FeedStatus::options())
                    ->query(function ($query, array $data) {
                        $status = FeedStatus::tryFrom($data['value'] ?? '');

                        return $status ? $status->constrain($query) : $query;
                    }),
                Tables\Filters\TernaryFilter::make('is_visible'),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                // Bulk sizing: for a run of parts that all go in the same
                // satchel or box, rather than one part at a time.
                Tables\Actions\BulkAction::make('setShippingBand')
                    ->label('Set freight size')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('shipping_band')
                            ->label('Freight size')
                            ->options(ShippingBand::optionsWithRates())
                            ->required()
                            ->native(false),
                    ])
                    ->action(fn ($records, array $data) => $records->each->update(['shipping_band' => $data['shipping_band']]))
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('markSold')
                    ->label('Mark as sold')
                    ->icon('heroicon-o-check-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($records) => $records->each->markSold())
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('markAvailable')
                    ->label('Mark as available')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($records) => $records->each->markAvailable())
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'stock_number', 'description'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParts::route('/'),
            'create' => Pages\CreatePart::route('/create'),
            'edit' => Pages\EditPart::route('/{record}/edit'),
        ];
    }
}
