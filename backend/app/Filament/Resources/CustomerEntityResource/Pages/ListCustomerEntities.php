<?php

namespace App\Filament\Resources\CustomerEntityResource\Pages;

use App\Filament\Resources\CustomerEntityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Сторінка зі списком клієнтів
 */
class ListCustomerEntities extends ListRecords
{
    protected static string $resource = CustomerEntityResource::class;

    /**
     * Повертає дії для заголовка сторінки
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
