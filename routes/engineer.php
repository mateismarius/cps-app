<?php

use App\Http\Controllers\Engineer\DashboardController;
use App\Http\Controllers\Engineer\FinanceController;
use App\Http\Controllers\Engineer\InvoiceController;
use App\Http\Controllers\Engineer\ReportController;
use App\Http\Controllers\Engineer\ScheduleController;
use App\Http\Controllers\Engineer\TimesheetController;
use Illuminate\Support\Facades\Route;

// Engineer routes - protected by auth and engineer role
Route::middleware(['auth', 'verified', 'role:engineer'])
    ->prefix('engineer')
    ->name('engineer.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Schedules
        Route::get('/schedules', [ScheduleController::class, 'index'])
            ->name('schedules.index');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/reports/create', [ReportController::class, 'create'])
            ->name('reports.create');

        Route::post('/reports', [ReportController::class, 'store'])
            ->name('reports.store');

        Route::get('/reports/{report}', [ReportController::class, 'show'])
            ->name('reports.show');

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index'])
            ->name('invoices.index');

        Route::get('/invoices/generate', [InvoiceController::class, 'generate'])
            ->name('invoices.generate');

        Route::post('/invoices', [InvoiceController::class, 'store'])
            ->name('invoices.store');

        // Finance
        Route::get('/finance', [FinanceController::class, 'index'])
            ->name('finance.index');

        Route::get('/timesheet', [TimesheetController::class, 'index'])->name('engineer.timesheet.index');
        Route::post('/timesheet', [TimesheetController::class, 'store'])->name('engineer.timesheet.store');
        Route::patch('/timesheet/{timesheet}', [TimesheetController::class, 'update'])->name('engineer.timesheet.update');
    });
