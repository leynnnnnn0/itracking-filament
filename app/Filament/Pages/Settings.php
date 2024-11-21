<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.settings';


    public function performDatabaseBackup()
    {
        try {
            // Run the backup
            Artisan::call('db:backup');
            $backupsPath = storage_path('app/backups');

            $backupFiles = File::glob($backupsPath . '/*.sql');

            $latestBackupFile = $backupFiles[sizeof($backupFiles) - 1];
            $this->dispatch('close-modal', id: 'database-backup');
            return response()->download($latestBackupFile);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Backup Failed')
                ->body('An error occurred during the backup process: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function confirmDatabaseRestore()
    {
        $this->dialog()->confirm([
            'title'       => 'Restore Database',
            'description' => 'Are you sure you want to restore the database? This action cannot be undone.',
            'icon'        => 'heroicon-o-exclamation-triangle',
            'accept'      => [
                'label'  => 'Yes, Restore',
                'method' => 'performDatabaseRestore',
            ],
            'reject' => [
                'label' => 'No, Cancel',
            ],
        ]);
    }

    public function performDatabaseRestore()
    {
        try {
            // Implement your restore logic here
            // Example: Using a specific backup file or latest backup
            \Artisan::call('backup:restore');

            Notification::make()
                ->title('Restore Successful')
                ->body('Your database has been restored successfully.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Restore Failed')
                ->body('An error occurred during the restore process: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadUserManual()
    {
        try {
            return redirect()->route('user-manual');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Download Error')
                ->body('An error occurred: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
