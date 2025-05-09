<?php

namespace App\Filament\Resources\CustomerEntityResource\Pages;

use App\Filament\Resources\CustomerEntityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewCustomerEntity extends ViewRecord
{
    protected static string $resource = CustomerEntityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Інформація про клієнта')
                    ->schema([
                        Infolists\Components\TextEntry::make('entity_id')
                            ->label('ID'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('first_name')
                            ->label('Ім\'я'),
                        Infolists\Components\TextEntry::make('last_name')
                            ->label('Прізвище'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Статистика')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Дата реєстрації')
                            ->dateTime('d.m.Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Дата оновлення')
                            ->dateTime('d.m.Y H:i'),
                        Infolists\Components\TextEntry::make('orders_count')
                            ->label('Кількість замовлень')
                            ->state(function ($record) {
                                return $record->orders()->count();
                            }),
                        Infolists\Components\TextEntry::make('total_spent')
                            ->label('Всього витрачено')
                            ->state(function ($record) {
                                return $record->orders()->sum('grand_total');
                            })
                            ->money('UAH'),
                    ])
                    ->columns(2),
            ]);
    }
} 