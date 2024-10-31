<?php

use App\Filament\Resources\EquipmentResource\Pages\ListEquipment;
use App\Http\Controllers\Pdf\EquipmentPdf;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/equipment-pdf/{equipment}/{personnel}', EquipmentPdf::class)->name('equipment-pdf');

   

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
