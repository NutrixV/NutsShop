<?php

namespace App\Filament\Resources\CustomerEntityResource\Pages;

use App\Filament\Resources\CustomerEntityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerEntity extends CreateRecord
{
    protected static string $resource = CustomerEntityResource::class;
}
