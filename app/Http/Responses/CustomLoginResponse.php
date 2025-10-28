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

        // Redirect based on user role
        return redirect()->intended(match (true) {
            $user->hasRole('engineer') && !$user->hasRole('super_admin') => '/engineer',
            $user->hasRole('super_admin') => filament()->getUrl(), // stays on /admin
            default => filament()->getUrl(),
        });
    }
}
