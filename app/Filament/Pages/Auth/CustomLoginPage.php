<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Pages\Auth\Login;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class CustomLoginPage extends Login
{
    public function authenticate(): LoginResponse
    {
        $credentials = $this->form->getState();

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }
        return parent::authenticate();
    }
}
