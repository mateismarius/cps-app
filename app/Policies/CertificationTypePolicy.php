<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CertificationType;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificationTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CertificationType');
    }

    public function view(AuthUser $authUser, CertificationType $certificationType): bool
    {
        return $authUser->can('View:CertificationType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CertificationType');
    }

    public function update(AuthUser $authUser, CertificationType $certificationType): bool
    {
        return $authUser->can('Update:CertificationType');
    }

    public function delete(AuthUser $authUser, CertificationType $certificationType): bool
    {
        return $authUser->can('Delete:CertificationType');
    }

    public function restore(AuthUser $authUser, CertificationType $certificationType): bool
    {
        return $authUser->can('Restore:CertificationType');
    }

    public function forceDelete(AuthUser $authUser, CertificationType $certificationType): bool
    {
        return $authUser->can('ForceDelete:CertificationType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CertificationType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CertificationType');
    }

    public function replicate(AuthUser $authUser, CertificationType $certificationType): bool
    {
        return $authUser->can('Replicate:CertificationType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CertificationType');
    }

}