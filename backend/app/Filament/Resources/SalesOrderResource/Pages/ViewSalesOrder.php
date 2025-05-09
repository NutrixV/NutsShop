<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewSalesOrder extends ViewRecord
{
    protected static string $resource = SalesOrderResource::class;

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
                Infolists\Components\Section::make('Інформація про замовлення')
                    ->schema([
                        Infolists\Components\TextEntry::make('increment_id')
                            ->label('№ замовлення'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Дата створення')
                            ->dateTime('d.m.Y H:i'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Статус')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Очікує обробки',
                                'processing' => 'В обробці',
                                'completed' => 'Завершено',
                                'cancelled' => 'Скасовано',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'processing' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Інформація про покупця')
                    ->schema([
                        Infolists\Components\TextEntry::make('customer_id')
                            ->label('ID Клієнта')
                            ->formatStateUsing(fn ($state) => $state ?: 'Гість'),
                        Infolists\Components\TextEntry::make('shipping_address')
                            ->label('Інформація про доставку')
                            ->formatStateUsing(function ($state) {
                                if (is_string($state)) {
                                    $state = json_decode($state, true);
                                }
                                
                                if (!$state) {
                                    return 'Н/Д';
                                }
                                
                                $parts = [
                                    "Ім'я: {$state['first_name']} {$state['last_name']}",
                                    "Email: {$state['email']}",
                                    "Телефон: {$state['phone']}",
                                    "Адреса: {$state['address']}, {$state['city']}, {$state['region']}, {$state['postcode']}",
                                ];
                                
                                if (!empty($state['notes'])) {
                                    $parts[] = "Примітки: {$state['notes']}";
                                }
                                
                                return implode("<br>", $parts);
                            })
                            ->html(),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Інформація про оплату')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Спосіб оплати')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'cash' => 'Готівка при отриманні',
                                'card' => 'Оплата карткою',
                                default => $state ?: 'Н/Д',
                            }),
                        Infolists\Components\TextEntry::make('subtotal')
                            ->label('Сума товарів')
                            ->money('UAH'),
                        Infolists\Components\TextEntry::make('grand_total')
                            ->label('Загальна сума')
                            ->money('UAH'),
                        Infolists\Components\TextEntry::make('currency')
                            ->label('Валюта'),
                    ])
                    ->columns(2),
            ]);
    }
} 