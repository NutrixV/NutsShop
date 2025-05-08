<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatalogProductResource\Pages;
use App\Filament\Resources\CatalogProductResource\RelationManagers;
use App\Models\CatalogProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CatalogProductResource extends Resource
{
    protected static ?string $model = CatalogProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationLabel = 'Продукти';
    
    protected static ?string $navigationGroup = 'Каталог';
    
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Зображення')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Зображення')
                            ->image()
                            ->directory('product-images')
                            ->disk('public')
                            ->required(false)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                        Forms\Components\FileUpload::make('gallery')
                            ->label('Галерея')
                            ->multiple()
                            ->image()
                            ->directory('product-gallery')
                            ->disk('public')
                            ->required(false)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxFiles(5),
                    ]),
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(64),
                        Forms\Components\TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Опис')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('short_description')
                            ->label('Короткий опис')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('price')
                            ->label('Ціна')
                            ->required()
                            ->numeric()
                            ->prefix('₴'),
                        Forms\Components\TextInput::make('base_currency')
                            ->label('Валюта')
                            ->default('UAH')
                            ->maxLength(3),
                        Forms\Components\TextInput::make('qty')
                            ->label('Кількість')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_in_stock')
                            ->label('В наявності')
                            ->default(true),
                        Forms\Components\Select::make('visibility')
                            ->label('Видимість')
                            ->options([
                                1 => 'Не видимий',
                                2 => 'Каталог',
                                3 => 'Пошук',
                                4 => 'Каталог та пошук',
                            ])
                            ->default(4),
                        Forms\Components\Toggle::make('status')
                            ->label('Статус')
                            ->default(1),
                    ])->columns(2),
                
                Forms\Components\Section::make('Специфікації')
                    ->schema([
                        Forms\Components\TextInput::make('nut_type')
                            ->label('Тип горіха')
                            ->maxLength(64),
                        Forms\Components\TextInput::make('sweetness_level')
                            ->label('Рівень солодкості')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10),
                        Forms\Components\TextInput::make('cocoa_pct')
                            ->label('Відсоток какао')
                            ->numeric()
                            ->suffix('%'),
                        Forms\Components\Toggle::make('salted')
                            ->label('Солоний'),
                        Forms\Components\Toggle::make('roasted')
                            ->label('Обсмажений'),
                        Forms\Components\Toggle::make('gluten_free')
                            ->label('Без глютену')
                            ->default(true),
                        Forms\Components\Toggle::make('organic')
                            ->label('Органічний')
                            ->default(false),
                        Forms\Components\Select::make('origin_country')
                            ->label('Країна походження')
                            ->options([
                                'UA' => 'Україна',
                                'US' => 'США',
                                'TR' => 'Туреччина',
                                'CH' => 'Швейцарія',
                                'IT' => 'Італія',
                            ]),
                        Forms\Components\TextInput::make('weight_g')
                            ->label('Вага (г)')
                            ->numeric(),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('Термін придатності'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Категорії')
                    ->schema([
                        Forms\Components\CheckboxList::make('categories')
                            ->label('Категорії')
                            ->relationship('categories', 'name')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Зображення')
                    ->disk('public')
                    ->width(100)
                    ->height(70),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Ціна')
                    ->money('UAH')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Кількість')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_in_stock')
                    ->label('В наявності')
                    ->boolean(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Статус')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListCatalogProducts::route('/'),
            'create' => Pages\CreateCatalogProduct::route('/create'),
            'edit' => Pages\EditCatalogProduct::route('/{record}/edit'),
        ];
    }
}
