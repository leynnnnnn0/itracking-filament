<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Settings extends Page
{
    public $backupFile;
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.settings';


    public function performDatabaseBackup()
    {
        try {
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

    public function performDatabaseRestore()
    {
        try {
            if (!$this->backupFile) {
                Notification::make()
                    ->title('Restore Failed')
                    ->body('Please upload a valid SQL backup file.')
                    ->danger()
                    ->send();
                return;
            }

            $path = $this->backupFile->store('temp-backups', 'local');
            $fullPath = Storage::path($path);

            DB::table('sessions')->truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            $sql = file_get_contents($fullPath);

            $statements = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    try {
                        DB::statement($statement);
                    } catch (\Exception $statementError) {
                        Log::warning('SQL Statement Error: ' . $statementError->getMessage());
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->dispatch('close-modal', id: 'database-restore');

            Auth::guard('web')->logout();
            session()->invalidate();
            session()->regenerateToken();

            Notification::make()
                ->title('Database Restored')
                ->body('Database has been successfully restored. Please log in again.')
                ->success()
                ->send();

            return;
        } catch (\Exception $e) {

            Log::error('Database Restore Failed: ' . $e->getMessage());

            Notification::make()
                ->title('Restore Failed')
                ->body('An error occurred during database restoration: ' . $e->getMessage())
                ->danger()
                ->send();

            return back();
        } finally {
            if (isset($path)) {
                Storage::delete($path);
            }
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
