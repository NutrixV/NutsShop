<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatalogCategoryResource\Pages;
use App\Filament\Resources\CatalogCategoryResource\RelationManagers;
use App\Models\CatalogCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CatalogCategoryResource extends Resource
{
    protected static ?string $model = CatalogCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Категорії';
    
    protected static ?string $navigationGroup = 'Каталог';
    
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make('Інформація про категорію')
                            ->schema([
                                Forms\Components\Select::make('parent_id')
                                    ->label('Батьківська категорія')
                                    ->relationship('parent', 'name')
                                    ->preload(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Назва')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('url_key')
                                    ->label('URL-ключ')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Активна')
                                    ->default(true),
                                Forms\Components\TextInput::make('position')
                                    ->label('Позиція')
                                    ->integer()
                                    ->default(10),
                            ]),
                        Forms\Components\Section::make('Зображення')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Зображення')
                                    ->image()
                                    ->directory('category-images')
                                    ->disk('public')
                                    ->required(false)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                            ]),
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
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Батьківська категорія')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url_key')
                    ->label('URL-ключ')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Позиція')
                    ->sortable(),
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
            'index' => Pages\ListCatalogCategories::route('/'),
            'create' => Pages\CreateCatalogCategory::route('/create'),
            'edit' => Pages\EditCatalogCategory::route('/{record}/edit'),
        ];
    }
}
