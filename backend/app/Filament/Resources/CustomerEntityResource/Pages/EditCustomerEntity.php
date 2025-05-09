<?php

namespace App\Filament\Resources\CustomerEntityResource\Pages;

use App\Filament\Resources\CustomerEntityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Сторінка редагування клієнта
 */
class EditCustomerEntity extends EditRecord
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
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }
}
