<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Filament\Models\Contracts\FilamentUser;


class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function engineer(): HasOne
    {
        return $this->hasOne(Engineer::class);
    }

    public function hasEnabledTwoFactorAuthentication(): bool
    {
        return ! is_null($this->two_factor_secret) &&
            ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Determine which Filament panels the user can access
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return match($panel->getId()) {
            'admin' => $this->hasRole(['super_admin']),
            'engineer' => $this->hasRole('engineer'),
            default => false,
        };
    }

    /**
     * Get the default panel for this user
     */
    public function getFilamentDefaultPanel(): string
    {
        if ($this->hasRole('super_admin')) {
            return 'admin';
        }

        if ($this->hasRole('engineer')) {
            return 'engineer';
        }

        return 'admin';
    }
}
