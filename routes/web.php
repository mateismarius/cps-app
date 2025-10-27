<?php

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirect root către admin
Route::get('/', function () {
    // Dacă user e autentificat, redirect către panelul său
    if (auth()->check()) {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return redirect('/admin');
        }

        if ($user->hasRole('engineer')) {
            return redirect('/engineer');
        }

        // Default admin pentru orice alt rol
        return redirect('/admin');
    }

    // Dacă nu e autentificat, redirect la login
    return redirect('/admin/login');
})->name('home');

//Route::middleware(['auth', 'verified'])->group(function () {
//    Route::get('dashboard', function () {
//        return Inertia::render('dashboard');
//    })->name('dashboard');
//});

Route::middleware(['auth'])->group(function () {
    Route::get('/invoices/{invoice}/pdf', function (Invoice $invoice) {
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice->load(['items', 'invoiceable']),
        ]);

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    })->name('invoices.pdf');
});

Route::any('register', fn() => abort(404))->name('register');
Route::any('register/{any}', fn() => abort(404))->where('any', '.*');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
//require __DIR__.'/engineer.php';
