<?php

// app/Observers/CertificationObserver.php

namespace App\Observers;

use App\Models\Certification;
use App\Notifications\CertificationExpiringNotification;

class CertificationObserver
{
    /**
     * Handle the Certification "updated" event.
     */
    public function updated(Certification $certification): void
    {
        // Check if certification is expiring soon
        if ($certification->status === 'expiring_soon' &&
            $certification->wasChanged('status')) {
            $this->notifyExpiringCertification($certification);
        }
    }

    protected function notifyExpiringCertification(Certification $certification): void
    {
        // Notify relevant users about expiring certification
        $admins = \App\Models\User::role(['super_admin', 'operations_manager'])->get();

        foreach ($admins as $admin) {
            $admin->notify(new CertificationExpiringNotification($certification));
        }
    }
}
