<?php

namespace App\Filament\Resources\CustomerEntityResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Менеджер відносин для адрес клієнтів
 */
class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';
    
    protected static ?string $recordTitleAttribute = 'city';
    
    protected static ?string $title = 'Адреси';

    /**
     * Схема форми для редагування/створення адреси
     *
     * @param  Form  $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('Ім\'я')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('last_name')
                    ->label('Прізвище')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('phone')
                    ->label('Телефон')
                    ->tel()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('city')
                    ->label('Місто')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('region')
                    ->label('Область')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('postcode')
                    ->label('Поштовий індекс')
                    ->required()
                    ->maxLength(32),
                    
                Forms\Components\Textarea::make('address')
                    ->label('Адреса')
                    ->required()
                    ->maxLength(512),
            ]);
    }

    /**
     * Схема таблиці для відображення адрес
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Ім\'я'),
                    
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Прізвище'),
                    
                Tables\Columns\TextColumn::make('city')
                    ->label('Місто')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('region')
                    ->label('Область'),
                    
                Tables\Columns\TextColumn::make('address')
                    ->label('Адреса')
                    ->limit(30),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
} 