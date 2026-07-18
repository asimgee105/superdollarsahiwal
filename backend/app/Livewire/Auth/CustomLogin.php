<?php

namespace App\Livewire\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class CustomLogin extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.custom-login';

    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException $e) {
            $this->dispatch('login-failed');
            throw $e;
        }
    }
}
