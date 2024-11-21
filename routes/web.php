<?php

use App\Filament\Resources\EquipmentResource\Pages\ListEquipment;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\Pdf\EquipmentPdf;
use App\Http\Controllers\RestoreDatabaseController;
use App\Http\Controllers\UserManualController;
use App\Http\Controllers\UserManualViewController;
use App\Livewire\DeleteArchive;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/equipment-pdf/{equipment}/{personnel}/{previousAccountableOfficer}', EquipmentPdf::class)->name('equipment-pdf');

    Route::get('/database-backup', [DatabaseBackupController::class, 'backup'])
        ->name('database.backup')
        ->middleware(['auth']);

    Route::get('/user-manual', UserManualController::class)
        ->name('user-manual')
        ->middleware(['auth']);

    Route::get('/restore-database', RestoreDatabaseController::class)
        ->name('restore-database')
        ->middleware(['auth']);

    Route::get('/system-guide', [UserManualViewController::class, 'index']);
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// require __DIR__ . '/auth.php';
