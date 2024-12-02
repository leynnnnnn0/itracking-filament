<?php

namespace App\Filament\Resources\SUBICSMRResource\Pages;

use App\Filament\Resources\SUBICSMRResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSUBICSMRS extends ListRecords
{
    protected static string $resource = SUBICSMRResource::class;
    protected static ?string $title = 'Sub ICS/MR List';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Sub ICS/MR'),
        ];
    }
}
