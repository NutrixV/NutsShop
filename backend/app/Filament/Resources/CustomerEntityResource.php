<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerEntityResource\Pages;
use App\Filament\Resources\CustomerEntityResource\RelationManagers;
use App\Models\CustomerEntity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerEntityResource extends Resource
{
    protected static ?string $model = CustomerEntity::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Клієнти';
    
    protected static ?string $navigationGroup = 'Клієнти';
    
    protected static ?int $navigationSort = 10;
    
    protected static ?string $recordTitleAttribute = 'email';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name')
                    ->label('Ім\'я')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Прізвище')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Створено')
                    ->disabled()
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                Forms\Components\DateTimePicker::make('updated_at')
                    ->label('Оновлено')
                    ->disabled()
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity_id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Ім\'я')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Прізвище')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата реєстрації')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('К-сть замовлень')
                    ->counts('orders')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('registered_from')
                            ->label('Зареєстровані з'),
                        Forms\Components\DatePicker::make('registered_until')
                            ->label('по'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['registered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['registered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('has_orders')
                    ->label('З замовленнями')
                    ->query(fn (Builder $query): Builder => $query->has('orders')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\AddressesRelationManager::class,
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerEntities::route('/'),
            'create' => Pages\CreateCustomerEntity::route('/create'),
            'view' => Pages\ViewCustomerEntity::route('/{record}'),
            'edit' => Pages\EditCustomerEntity::route('/{record}/edit'),
        ];
    }
}
