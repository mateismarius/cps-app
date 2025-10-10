<?php

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/invoices/{invoice}/pdf', function (Invoice $invoice) {
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice->load(['items', 'invoiceable']),
        ]);

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    })->name('invoices.pdf');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
