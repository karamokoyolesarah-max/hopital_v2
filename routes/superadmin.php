<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

// --- 1. ACCÈS PUBLIC / GUEST (Login) ---
Route::prefix('admin-system')->group(function () {
    Route::middleware('guest:superadmin')->group(function () {
        Route::get('/login', [SuperAdminController::class, 'showLoginForm'])->name('superadmin.login');
        Route::post('/login', [SuperAdminController::class, 'login'])->name('superadmin.login.post');
    });
});

// --- 2. ACCÈS RESTREINT (Vérification de sécurité / Webhooks) ---
Route::prefix('admin-system')->group(function () {
    
    // Webhook CinetPay (Hors authentification car appelé par un serveur externe)
    Route::post('/payment/cinetpay/webhook', [SubscriptionController::class, 'handleCinetpayWebhook'])
        ->name('superadmin.cinetpay.webhook');

    // Vérification du code (Nécessite juste d'être loggé)
    Route::middleware(['auth:superadmin'])->group(function () {
        Route::get('/verify', [SuperAdminController::class, 'showVerifyForm'])->name('superadmin.verify');
        Route::post('/verify', [SuperAdminController::class, 'verifyCode'])->name('superadmin.verify.post');
    });
});

// --- 3. ROUTES PROTÉGÉES (Connecté + Vérifié + Rôle Super Admin) ---
Route::middleware(['auth:superadmin', 'superadmin.verified', 'role:super_admin'])
    ->prefix('admin-system')
    ->group(function () {
    
    // Dashboard Principal
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');

    // --- GESTION DES HÔPITAUX ---
    Route::controller(SuperAdminController::class)->group(function () {
        Route::post('/hospitals/store', 'storeHospital')->name('superadmin.hospitals.store');
        Route::get('/hospitals/{hospital}/details', 'getHospitalDetails')->name('superadmin.hospitals.details');
        Route::post('/hospitals/{hospital}/toggle-status', 'toggleHospitalStatus')->name('superadmin.hospitals.toggle-status');
    });

    // --- GESTION DES SPÉCIALISTES ---
    Route::post('/specialists/{id}/validate', [SuperAdminController::class, 'validateSpecialist'])->name('superadmin.specialists.validate');
    Route::post('/specialists/{specialist}/block-wallet', [SuperAdminController::class, 'blockSpecialistWallet'])->name('superadmin.specialists.block-wallet');
    Route::post('/specialists/{specialist}/unblock-wallet', [SuperAdminController::class, 'unblockSpecialistWallet'])->name('superadmin.specialists.unblock-wallet');
    Route::post('/specialists/{specialist}/adjust-balance', [SuperAdminController::class, 'adjustSpecialistBalance'])->name('superadmin.specialists.adjust-balance');
    Route::post('/specialists/activation', [SuperAdminController::class, 'processSpecialistActivation'])->name('superadmin.specialists.activation');

    // --- ABONNEMENTS (SUBSCRIPTIONS) ---
    Route::get('/subscription-plans', [SuperAdminController::class, 'getSubscriptionPlans'])
        ->name('superadmin.subscription-plans.index');
    Route::post('/subscription-plans', [SuperAdminController::class, 'storeSubscriptionPlan'])
        ->name('superadmin.subscription-plans.store');
    Route::put('/subscription-plans/{plan}', [SuperAdminController::class, 'updateSubscriptionPlan'])
        ->name('superadmin.subscription-plans.update');
    Route::delete('/subscription-plans/{plan}', [SuperAdminController::class, 'deleteSubscriptionPlan'])
        ->name('superadmin.subscription-plans.destroy');

    // --- COMMISSIONS & FINANCE ---
    Route::get('/commission-rates', [SuperAdminController::class, 'getCommissionRates'])
        ->name('superadmin.commission-rates.index');
    Route::get('/commission-rates/{rate}', [SuperAdminController::class, 'showCommissionRate'])
        ->name('superadmin.commission-rates.show');
    Route::post('/commission-rates', [SuperAdminController::class, 'storeCommissionRate'])
        ->name('superadmin.commission-rates.store');
    Route::put('/commission-rates/{rate}', [SuperAdminController::class, 'updateCommissionRate'])
        ->name('superadmin.commission-rates.update');
    Route::delete('/commission-rates/{rate}', [SuperAdminController::class, 'deleteCommissionRate'])
        ->name('superadmin.commission-rates.destroy');
    Route::get('/financial-monitoring', [SuperAdminController::class, 'getFinancialMonitoring'])->name('superadmin.financial-monitoring');
    Route::get('/invoices', [SuperAdminController::class, 'getInvoices'])->name('superadmin.invoices.index');
    Route::post('/commission/deduct', [SuperAdminController::class, 'deductCommission'])->name('superadmin.commission.deduct');

    // --- LOGOUT ---
    Route::post('/logout', [SuperAdminController::class, 'logout'])->name('superadmin.logout');
});