<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Subcontractor;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubcontractorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Subcontractor');
    }

    public function view(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $authUser->can('View:Subcontractor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Subcontractor');
    }

    public function update(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $authUser->can('Update:Subcontractor');
    }

    public function delete(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $authUser->can('Delete:Subcontractor');
    }

    public function restore(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $authUser->can('Restore:Subcontractor');
    }

    public function forceDelete(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $authUser->can('ForceDelete:Subcontractor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Subcontractor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Subcontractor');
    }

    public function replicate(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $authUser->can('Replicate:Subcontractor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Subcontractor');
    }

}