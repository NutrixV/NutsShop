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
 * Менеджер відносин для замовлень клієнта
 */
class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';
    
    protected static ?string $recordTitleAttribute = 'increment_id';
    
    protected static ?string $title = 'Замовлення';

    /**
     * Схема форми для редагування замовлення
     *
     * @param  Form  $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'В очікуванні',
                        'processing' => 'В обробці',
                        'completed' => 'Завершено',
                        'canceled' => 'Скасовано',
                    ])
                    ->required(),
            ]);
    }

    /**
     * Схема таблиці для відображення замовлень
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('increment_id')
                    ->label('Номер замовлення')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата створення')
                    ->dateTime('d.m.Y H:i'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'В очікуванні',
                        'processing' => 'В обробці',
                        'completed' => 'Завершено',
                        'canceled' => 'Скасовано',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Сума')
                    ->money('UAH'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'В очікуванні',
                        'processing' => 'В обробці',
                        'completed' => 'Завершено',
                        'canceled' => 'Скасовано',
                    ]),
            ])
            ->headerActions([
                // No create action as orders are created through the cart process
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.sales-orders.view', $record)),
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.sales-orders.edit', $record)),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
} 