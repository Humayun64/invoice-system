<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SettingsController;

// Redirect root to login or dashboard
Route::get('/', function () {
    return redirect()->route(session('user_id') ? 'dashboard' : 'login');
});

// Auth Routes
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth.check')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Invoices
    Route::get('/invoices',              [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create',       [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices',             [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}/edit',    [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{id}',         [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{id}',      [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{id}/print',   [InvoiceController::class, 'print'])->name('invoices.print');

    // Quotations
    Route::get('/quotations',              [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create',       [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations',             [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{id}/edit',    [QuotationController::class, 'edit'])->name('quotations.edit');
    Route::put('/quotations/{id}',         [QuotationController::class, 'update'])->name('quotations.update');
    Route::delete('/quotations/{id}',      [QuotationController::class, 'destroy'])->name('quotations.destroy');
    Route::get('/quotations/{id}/print',   [QuotationController::class, 'print'])->name('quotations.print');
    Route::post('/quotations/{id}/convert',[QuotationController::class, 'convertToInvoice'])->name('quotations.convert');

    // Clients
    Route::get('/clients',          [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients',         [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{id}/edit',[ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{id}',     [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{id}',  [ClientController::class, 'destroy'])->name('clients.destroy');

    // Settings
    Route::get('/settings',              [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/company',     [SettingsController::class, 'updateCompany'])->name('settings.company');
    Route::post('/settings/password',    [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/logo',        [SettingsController::class, 'uploadLogo'])->name('settings.logo');
    Route::post('/settings/badge',       [SettingsController::class, 'addBadge'])->name('settings.badge.add');
    Route::delete('/settings/badge/{id}',[SettingsController::class, 'removeBadge'])->name('settings.badge.remove');
    Route::post('/settings/sign',        [SettingsController::class, 'uploadSign'])->name('settings.sign.upload');
    Route::post('/settings/sign/remove', [SettingsController::class, 'removeSign'])->name('settings.sign.remove');
});
