<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup the database';

    public function handle()
    {
        $database = Config::get('database.connections.mysql.database');
        $user = Config::get('database.connections.mysql.username');
        $pass = Config::get('database.connections.mysql.password');

        $path = storage_path('app/backups/');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $fileName = "backup-" . date('Y-m-d-H-i-s') . ".sql";

        $command = "\"C:\\xampp\\mysql\\bin\\mysqldump.exe\" --user={$user} --password={$pass} {$database} > {$path}{$fileName}";

        exec($command);

        $this->info('Database backup completed successfully.');
    }
}
