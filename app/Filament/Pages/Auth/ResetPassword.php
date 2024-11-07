<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Page;

class ResetPassword extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.auth.reset-password';
}
