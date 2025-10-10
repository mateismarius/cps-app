<?php

// app/Console/Commands/UpdateCertificationStatus.php

namespace App\Console\Commands;

use App\Models\Certification;
use Illuminate\Console\Command;

class UpdateCertificationStatus extends Command
{
    protected $signature = 'certifications:update-status';
    protected $description = 'Update certification statuses based on expiry dates';

    public function handle()
    {
        $this->info('Updating certification statuses...');

        // Mark expired certifications
        $expired = Certification::where('expiry_date', '<', now())
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired']);

        // Mark expiring soon certifications
        $expiringSoon = Certification::whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->where('status', 'valid')
            ->update(['status' => 'expiring_soon']);

        $this->info("Updated {$expired} expired certifications");
        $this->info("Updated {$expiringSoon} expiring soon certifications");

        return 0;
    }
}
