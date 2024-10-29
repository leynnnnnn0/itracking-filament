<?php

namespace App\Filament\Resources\FundResource\Pages;

use App\Filament\Resources\FundResource;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFund extends CreateRecord
{
    use HasRedirectUrl;
    protected static string $resource = FundResource::class;
}
