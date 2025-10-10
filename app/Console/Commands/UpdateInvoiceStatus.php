<?php

// app/Console/Commands/UpdateInvoiceStatus.php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class UpdateInvoiceStatus extends Command
{
    protected $signature = 'invoices:update-status';
    protected $description = 'Update invoice statuses based on due dates';

    public function handle()
    {
        $this->info('Updating invoice statuses...');

        // Mark overdue invoices
        $overdue = Invoice::where('due_date', '<', now())
            ->where('status', 'sent')
            ->update(['status' => 'overdue']);

        $this->info("Marked {$overdue} invoices as overdue");

        return 0;
    }
}
