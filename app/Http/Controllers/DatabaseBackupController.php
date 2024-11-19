<?php

namespace App\Http\Controllers;

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DatabaseBackupController extends Controller
{
    public function backup()
    {
        try {
            Artisan::call('db:backup');

            Notification::make()
                ->title('Database backup completed successfully!')
                ->success()
                ->send();

            return redirect()->back();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Database backup failed!')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();

            return redirect()->back();
        }
    }
}
