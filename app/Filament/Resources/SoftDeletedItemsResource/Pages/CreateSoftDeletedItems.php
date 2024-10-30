<?php

namespace App\Filament\Resources\SoftDeletedItemsResource\Pages;

use App\Filament\Resources\SoftDeletedItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSoftDeletedItems extends CreateRecord
{
    protected static string $resource = SoftDeletedItemsResource::class;
}
