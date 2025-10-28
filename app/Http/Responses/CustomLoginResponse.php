<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();
        $panel = Filament::getCurrentPanel();

        // If logging in from engineer panel, stay there
        if ($panel->getId() === 'engineer') {
            return redirect('/engineer');
        }

        // If logging in from admin panel, redirect based on role
        if ($user->hasRole('engineer') && !$user->hasRole('super_admin')) {
            return redirect('/engineer');
        }

        return redirect('/admin');
    }
}
