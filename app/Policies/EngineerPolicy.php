<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Engineer;
use Illuminate\Auth\Access\HandlesAuthorization;

class EngineerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Engineer');
    }

    public function view(AuthUser $authUser, Engineer $engineer): bool
    {
        return $authUser->can('View:Engineer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Engineer');
    }

    public function update(AuthUser $authUser, Engineer $engineer): bool
    {
        return $authUser->can('Update:Engineer');
    }

    public function delete(AuthUser $authUser, Engineer $engineer): bool
    {
        return $authUser->can('Delete:Engineer');
    }

    public function restore(AuthUser $authUser, Engineer $engineer): bool
    {
        return $authUser->can('Restore:Engineer');
    }

    public function forceDelete(AuthUser $authUser, Engineer $engineer): bool
    {
        return $authUser->can('ForceDelete:Engineer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Engineer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Engineer');
    }

    public function replicate(AuthUser $authUser, Engineer $engineer): bool
    {
        return $authUser->can('Replicate:Engineer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Engineer');
    }

}