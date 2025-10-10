<?php

// app/Services/InvoiceService.php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Timesheet;
use App\Models\Client;
use App\Models\Subcontractor;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Generate invoice for client based on approved timesheets
     */
    public function generateClientInvoice(Client $client, array $timesheetIds, array $additionalItems = []): Invoice
    {
        return DB::transaction(function () use ($client, $timesheetIds, $additionalItems) {
            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoiceable_type' => Client::class,
                'invoiceable_id' => $client->id,
                'invoice_type' => 'client',
                'invoice_date' => now(),
                'due_date' => now()->addDays($client->payment_terms_days),
                'vat_rate' => 20,
                'status' => 'draft',
            ]);

            // Add timesheet items
            $timesheets = Timesheet::whereIn('id', $timesheetIds)
                ->where('status', 'approved')
                ->whereDoesntHave('invoiceItems')
                ->get();

            foreach ($timesheets as $timesheet) {
                $amount = $timesheet->calculateAmount();

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'timesheet_id' => $timesheet->id,
                    'description' => sprintf(
                        '%s - %s on %s (%s shift)',
                        $timesheet->worker->full_name,
                        $timesheet->project->name,
                        $timesheet->work_date->format('d/m/Y'),
                        $timesheet->shift_type
                    ),
                    'quantity' => $timesheet->hours_worked,
                    'unit' => 'hours',
                    'unit_price' => $timesheet->rate_amount,
                    'amount' => $amount,
                ]);

                // Mark timesheet as invoiced
                $timesheet->update(['status' => 'invoiced']);
            }

            // Add additional custom items
            foreach ($additionalItems as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit' => $item['unit'] ?? 'item',
                    'unit_price' => $item['unit_price'],
                    'amount' => ($item['quantity'] ?? 1) * $item['unit_price'],
                ]);
            }

            // Calculate totals
            $invoice->calculateTotals();

            return $invoice;
        });
    }

    /**
     * Generate invoice for subcontractor based on their timesheets
     */
    public function generateSubcontractorInvoice(Subcontractor $subcontractor, array $timesheetIds): Invoice
    {
        return DB::transaction(function () use ($subcontractor, $timesheetIds) {
            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoiceable_type' => Subcontractor::class,
                'invoiceable_id' => $subcontractor->id,
                'invoice_type' => 'subcontractor',
                'invoice_date' => now(),
                'due_date' => now()->addDays(30),
                'vat_rate' => $subcontractor->business_type === 'ltd' ? 20 : 0,
                'status' => 'draft',
            ]);

            $timesheets = Timesheet::whereIn('id', $timesheetIds)
                ->where('status', 'approved')
                ->whereHas('worker', function ($query) use ($subcontractor) {
                    $query->where('subcontractor_id', $subcontractor->id);
                })
                ->whereDoesntHave('invoiceItems')
                ->get();

            foreach ($timesheets as $timesheet) {
                $amount = $timesheet->calculateAmount();

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'timesheet_id' => $timesheet->id,
                    'description' => sprintf(
                        '%s - %s on %s',
                        $timesheet->worker->full_name,
                        $timesheet->project->name,
                        $timesheet->work_date->format('d/m/Y')
                    ),
                    'quantity' => $timesheet->hours_worked,
                    'unit' => 'hours',
                    'unit_price' => $timesheet->rate_amount,
                    'amount' => $amount,
                ]);

                $timesheet->update(['status' => 'invoiced']);
            }

            $invoice->calculateTotals();

            return $invoice;
        });
    }

    /**
     * Generate payslip for employee
     */
    public function generatePayslip($employeeId, $startDate, $endDate): Invoice
    {
        // Implementation for payslip generation
        // This would calculate salary, deductions, etc.
        return new Invoice();
    }

    /**
     * Generate unique invoice number
     */
    protected function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $count = Invoice::whereYear('created_at', $year)->count() + 1;

        return sprintf('INV-%s-%04d', $year, $count);
    }

    /**
     * Calculate invoice totals
     */
    public function calculateInvoiceTotals(Invoice $invoice): void
    {
        $invoice->calculateTotals();
    }

    /**
     * Get uninvoiced timesheets for a client
     */
    public function getUninvoicedTimesheets(Client $client)
    {
        return Timesheet::whereHas('project', function ($query) use ($client) {
            $query->where('client_id', $client->id);
        })
            ->where('status', 'approved')
            ->whereDoesntHave('invoiceItems')
            ->with(['worker', 'project'])
            ->get();
    }

    /**
     * Get uninvoiced timesheets for a subcontractor
     */
    public function getUninvoicedTimesheetsForSubcontractor(Subcontractor $subcontractor)
    {
        return Timesheet::whereHas('worker', function ($query) use ($subcontractor) {
            $query->where('subcontractor_id', $subcontractor->id);
        })
            ->where('status', 'approved')
            ->whereDoesntHave('invoiceItems')
            ->with(['worker', 'project'])
            ->get();
    }
}



