<?php

namespace App\Filament\Resources\SalesOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Товари замовлення';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Назва')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sku')
                    ->label('Артикул')
                    ->required()
                    ->maxLength(64),
                Forms\Components\TextInput::make('price')
                    ->label('Ціна')
                    ->required()
                    ->numeric()
                    ->prefix('₴'),
                Forms\Components\TextInput::make('qty_ordered')
                    ->label('Кількість')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('row_total')
                    ->label('Сума')
                    ->required()
                    ->numeric()
                    ->prefix('₴'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('Артикул')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('price')
                    ->label('Ціна')
                    ->money('UAH')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty_ordered')
                    ->label('Кількість')
                    ->sortable(),
                Tables\Columns\TextColumn::make('row_total')
                    ->label('Сума')
                    ->money('UAH')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Зазвичай не дозволяємо додавати елементи через адмінку
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }
} 