<?php

namespace App\Filament\Resources;

use App\Enums\PromotionStatus;
use App\Enums\PromotionType;
use App\Filament\Resources\PromotionResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Unique promotion code')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generate')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function (Forms\Set $set) {
                                        $set('code', strtoupper(substr(uniqid(), -8)));
                                    })
                            ),

                        Forms\Components\Select::make('type')
                            ->required()
                            ->options(PromotionType::options())
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('conditions', [])),

                        Forms\Components\Select::make('status')
                            ->required()
                            ->options(PromotionStatus::options())
                            ->default(PromotionStatus::ACTIVE->value),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Schedule')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Start Date & Time'),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('End Date & Time'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Buy X Get Y Free Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('conditions.buy_quantity')
                            ->label('Buy Quantity (X)')
                            ->numeric()
                            ->required()
                            ->default(2)
                            ->minValue(1)
                            ->helperText('Number of items customer must buy'),

                        Forms\Components\TextInput::make('conditions.get_quantity')
                            ->label('Get Free Quantity (Y)')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->helperText('Number of free items customer gets'),

                        // Apply To Options
                        Forms\Components\Section::make('Apply To')
                            ->schema([
                                Forms\Components\Radio::make('conditions.apply_to_type')
                                    ->label('Apply promotion to:')
                                    ->options([
                                        'any' => 'Any eligible items',
                                        'specific_products' => 'Specific products',
                                        'specific_categories' => 'Specific categories',
                                    ])
                                    ->default('any')
                                    ->reactive()
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('conditions.apply_to_product_ids')
                                    ->label('Specific Product to Buy')
                                    ->options(Product::where('is_active', true)->pluck('name', 'id'))
                                    ->helperText('Select specific product that can be bought to qualify')
                                    ->visible(fn(Forms\Get $get) => $get('conditions.apply_to_type') === 'specific_products')
                                    ->required(fn(Forms\Get $get) => $get('conditions.apply_to_type') === 'specific_products'),

                                Forms\Components\Select::make('conditions.apply_to_category_ids')
                                    ->label('Specific Category to Buy From')
                                    ->options(Category::where('is_active', true)->pluck('name', 'id'))
                                    ->helperText('Select specific category that can be bought from to qualify')
                                    ->visible(fn(Forms\Get $get) => $get('conditions.apply_to_type') === 'specific_categories')
                                    ->required(fn(Forms\Get $get) => $get('conditions.apply_to_type') === 'specific_categories'),
                            ])
                            ->columnSpanFull(),

                        // Get For Options
                        Forms\Components\Section::make('Get Free')
                            ->schema([
                                Forms\Components\Radio::make('conditions.get_type')
                                    ->label('Get free:')
                                    ->options([
                                        'cheapest' => 'Cheapest eligible items',
                                        'specific_products' => 'Specific products',
                                    ])
                                    ->default('cheapest')
                                    ->reactive()
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('conditions.get_product_ids')
                                    ->label('Specific Product to Get Free')
                                    ->options(Product::where('is_active', true)->pluck('name', 'id'))
                                    ->helperText('Select specific product that will be given free')
                                    ->visible(fn(Forms\Get $get) => $get('conditions.get_type') === 'specific_products')
                                    ->required(fn(Forms\Get $get) => $get('conditions.get_type') === 'specific_products'),

                                Forms\Components\Toggle::make('conditions.apply_to_cheapest')
                                    ->label('Apply to Cheapest Items')
                                    ->default(true)
                                    ->helperText('Free items will be the cheapest eligible ones')
                                    ->visible(fn(Forms\Get $get) => $get('conditions.get_type') === 'cheapest')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->visible(fn(Forms\Get $get) => $get('type') === PromotionType::BUY_X_GET_Y_FREE->value),

                Forms\Components\Section::make('Step Discount Configuration')
                    ->schema([
                        Forms\Components\Repeater::make('conditions.discount_tiers')
                            ->label('Discount Tiers')
                            ->schema([
                                Forms\Components\TextInput::make('position')
                                    ->label('Item Position')
                                    ->numeric()
                                    ->required()
                                    ->minValue(2)
                                    ->helperText('Position in sorted cart (cheapest first)'),

                                Forms\Components\TextInput::make('percentage')
                                    ->label('Discount %')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->helperText('Discount percentage for this position'),
                            ])
                            ->columns(2)
                            ->defaultItems(3)
                            ->default([
                                ['position' => 2, 'percentage' => 20],
                                ['position' => 3, 'percentage' => 30],
                                ['position' => 5, 'percentage' => 50],
                            ])
                            ->helperText('Configure discount percentages based on item position (cheapest items first)'),
                    ])
                    ->columns(1)
                    ->visible(fn(Forms\Get $get) => $get('type') === PromotionType::STEP_DISCOUNT->value),

                Forms\Components\Section::make('Fixed Price Bundle Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('conditions.bundle_quantity')
                            ->label('Bundle Quantity')
                            ->numeric()
                            ->required()
                            ->default(3)
                            ->minValue(2)
                            ->helperText('Number of items required for bundle'),

                        Forms\Components\TextInput::make('conditions.bundle_price')
                            ->label('Bundle Price ($)')
                            ->numeric()
                            ->required()
                            ->default(30.00)
                            ->minValue(0.01)
                            ->step(0.01)
                            ->helperText('Fixed total price for the bundle'),

                        Forms\Components\Radio::make('conditions.bundle_type')
                            ->label('Apply bundle to:')
                            ->options([
                                'any' => 'Any items',
                                'specific_products' => 'Specific product',
                                'specific_categories' => 'Specific category',
                            ])
                            ->default('any')
                            ->reactive()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('conditions.eligible_product_ids')
                            ->label('Eligible Product')
                            ->options(Product::where('is_active', true)->pluck('name', 'id'))
                            ->helperText('Select product eligible for bundle')
                            ->visible(fn(Forms\Get $get) => $get('conditions.bundle_type') === 'specific_products')
                            ->required(fn(Forms\Get $get) => $get('conditions.bundle_type') === 'specific_products'),

                        Forms\Components\Select::make('conditions.eligible_category_ids')
                            ->label('Eligible Category')
                            ->options(Category::where('is_active', true)->pluck('name', 'id'))
                            ->helperText('Select category eligible for bundle')
                            ->visible(fn(Forms\Get $get) => $get('conditions.bundle_type') === 'specific_categories')
                            ->required(fn(Forms\Get $get) => $get('conditions.bundle_type') === 'specific_categories'),
                    ])
                    ->columns(2)
                    ->visible(fn(Forms\Get $get) => $get('type') === PromotionType::FIXED_PRICE_BUNDLE->value),

                Forms\Components\Section::make('Percentage Discount Configuration')
                    ->schema([
                        Forms\Components\Radio::make('conditions.discount_type')
                            ->label('Discount Type')
                            ->options([
                                'percentage' => 'Percentage Off',
                                'fixed_amount' => 'Fixed Amount Off',
                            ])
                            ->default('percentage')
                            ->reactive()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('conditions.discount_value')
                            ->label('Discount Value')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->helperText(fn(Forms\Get $get) => $get('conditions.discount_type') === 'percentage' 
                                ? 'Percentage value (e.g., 10 for 10% off)' 
                                : 'Fixed amount in dollars (e.g., 5.00 for $5 off)')
                            ->maxValue(fn(Forms\Get $get) => $get('conditions.discount_type') === 'percentage' ? 100 : null),

                        Forms\Components\Radio::make('conditions.apply_to_type')
                            ->label('Apply discount to:')
                            ->options([
                                'all' => 'All products',
                                'specific_products' => 'Specific products',
                                'specific_categories' => 'Specific categories',
                            ])
                            ->default('all')
                            ->reactive()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('conditions.eligible_product_ids')
                            ->label('Eligible Products')
                            ->options(Product::where('is_active', true)->pluck('name', 'id'))
                            ->multiple()
                            ->helperText('Select specific products eligible for discount')
                            ->visible(fn(Forms\Get $get) => $get('conditions.apply_to_type') === 'specific_products')
                            ->required(fn(Forms\Get $get) => $get('conditions.apply_to_type') === 'specific_products'),

                        Forms\Components\Select::make('conditions.eligible_category_ids')
                            ->label('Eligible Categories')
                            ->options(Category::where('is_active', true)->pluck('name', 'id'))
                            ->multiple()
                            ->helperText('Select specific categories eligible for discount')
                            ->visible(fn(Forms\Get $get) => $get('conditions.apply_to_type') === 'specific_categories')
                            ->required(fn(Forms\Get $get) => $get('conditions.apply_to_type') === 'specific_categories'),
                    ])
                    ->columns(2)
                    ->visible(fn(Forms\Get $get) => $get('type') === PromotionType::PERCENTAGE_DISCOUNT->value),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($state) => PromotionType::from($state)->label())
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => PromotionStatus::from($state)->color())
                    ->formatStateUsing(fn($state) => PromotionStatus::from($state)->label())
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(PromotionType::options()),

                Tables\Filters\SelectFilter::make('status')
                    ->options(PromotionStatus::options()),

                Tables\Filters\Filter::make('active_now')
                    ->label('Active Now')
                    ->query(fn(Builder $query) => $query->active()),
            ])
            ->actions([
                Tables\Actions\Action::make('pause')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->visible(fn(Promotion $record) => $record->status === PromotionStatus::ACTIVE)
                    ->requiresConfirmation()
                    ->action(function (Promotion $record) {
                        $record->update(['status' => PromotionStatus::PAUSED->value]);
                    })
                    ->successNotificationTitle('Promotion paused'),
                
                Tables\Actions\Action::make('resume')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn(Promotion $record) => $record->status === PromotionStatus::PAUSED)
                    ->requiresConfirmation()
                    ->action(function (Promotion $record) {
                        $record->update(['status' => PromotionStatus::ACTIVE->value]);
                    })
                    ->successNotificationTitle('Promotion resumed'),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
