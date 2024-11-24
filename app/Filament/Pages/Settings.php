<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Settings extends Page implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithRecord;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }
    public $backupFile;
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Restore Database')
                    ->description('Recover your system from a previous backup. Select and restore your database to a specific point in time, ensuring data integrity and system reliability.')
                    ->schema([
                        FileUpload::make('file')
                            ->preserveFilenames()
                            ->rule('mimes:sql')
                            ->directory('database-backups')
                            ->maxSize(50 * 1024)
                            ->helperText('Upload a .sql backup file (max 50MB)')
                            ->required()
                    ])
            ])
            ->statePath('data');
    }


    public function getBackupActionName()
    {
        return 'backupDatabase';
    }

    protected function getActions(): array
    {
        return [
            Action::make($this->getBackupActionName())
                ->label('Create database backup')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Confirm Database Backup')
                ->modalDescription('Are you sure you want to create a database backup? This action will create a copy of the current database backup')
                ->modalSubmitActionLabel('Yes, create backup')
                ->modalCancelActionLabel('No, cancel')
                ->action(fn() => $this->performDatabaseBackup())
        ];
    }

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

    public function confirmBackup(): Action
    {
        return Action::make('confirmBackup')
            ->label('Backup')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Confirm Database Backup')
            ->modalDescription('By clicking the confirm button the system will generate a database backup file that will be stored on the system and it will be the most latest database content.')
            ->modalSubmitActionLabel('Confirm')
            ->modalCancelActionLabel('No, cancel')
            ->action(fn() => $this->performDatabaseBackup());
    }

    public function confirmRestore(): Action
    {
        return Action::make('confirmRestore')
            ->label('RESTORE')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Confirm Database Restore')
            ->modalDescription('Are you sure you want to restore the database? This action cannot be undone and will log you out of the system.')
            ->modalSubmitActionLabel('Yes, restore database')
            ->modalCancelActionLabel('No, cancel')
            ->action(fn() => $this->performDatabaseRestore());
    }

    public function performDatabaseRestore()
    {
        try {

            $data = $this->form->getState();
            if (!isset($data['file'])) {
                throw new \Exception('No backup file was uploaded');
            }

            $uploadedFile = $data['file'];

            $path = storage_path("app/public/$uploadedFile");

            if (!file_exists($path)) {
                throw new \Exception('Backup file not found');
            }

            DB::beginTransaction();


            try {
                DB::table('sessions')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                $sql = file_get_contents($path);

                $statements = array_filter(
                    array_map(
                        'trim',
                        explode(';', $sql)
                    )
                );

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

                DB::commit();

                if ($uploadedFile instanceof TemporaryUploadedFile) {
                    unlink($path);
                } else {
                    Storage::delete($uploadedFile);
                }

                Auth::guard('web')->logout();
                session()->invalidate();
                session()->regenerateToken();

                Notification::make()
                    ->title('Database Restored')
                    ->body('Database has been successfully restored. Please log in again.')
                    ->success()
                    ->send();

                return redirect()->route('filament.admin.auth.login');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Database Restore Failed: ' . $e->getMessage());

            Notification::make()
                ->title('Restore Failed')
                ->body('An error occurred during database restoration: ' . $e->getMessage())
                ->danger()
                ->send();

            return back();
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
