<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

class RestoreDatabaseController extends Controller
{
    public function __invoke()
    {

        $backupsPath = storage_path('app/backups');

        $backupFiles = File::glob($backupsPath . '/*.sql');

        if (empty($backupFiles)) {
            return back()->withErrors(['backup_file' => 'No backup files found.']);
        }

        usort($backupFiles, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestBackupFile = $backupFiles[0];


        try {
            DB::transaction(function () use ($latestBackupFile) {
                $sql = file_get_contents($latestBackupFile);

                DB::unprepared($sql);
            });

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed')
                ->body($e)
                ->danger()
                ->send();
            return back();
        }
    }
}
