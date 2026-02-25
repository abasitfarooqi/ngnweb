<?php

use App\Http\Controllers\Admin\RecurringController;
use App\Http\Controllers\Judopay\JudopayController;
use Illuminate\Support\Facades\Route;

Route::prefix('judopay')
    ->middleware(['web'])
    ->group(function () {
        Route::post('webhook/enpqbMwqAXiU', [JudopayController::class, 'webhook'])->name('recurring-payment.webhook');
        Route::match(['get', 'post'], 'success/LnPqbMwqAXvCU', [JudopayController::class, 'success'])->name('cit-payment.success');
        Route::match(['get', 'post'], 'failure/enpWqTqAU', [JudopayController::class, 'failure'])->name('cit-payment.failure');
        // Route::post('create-cit-session', [JudopayController::class, 'createCitSession'])->name('judopay.create.cit.session');
    });

// Customer-facing authorization routes (outside admin middleware)
Route::prefix('payment')
    ->group(function () {
        Route::get('/authorize/{customer_id}/{passcode}/{subscription_id}', [RecurringController::class, 'showAuthorizationForm'])->name('payment.authorize');
        Route::post('/authorize/{customer_id}/{passcode}/{subscription_id}/send-verification-code', [RecurringController::class, 'sendAuthorizationSms'])->name('payment.authorize.send-sms');
        Route::post('/authorize/{customer_id}/{passcode}/{subscription_id}/verify-code', [RecurringController::class, 'processAuthorizationConsent'])->name('payment.authorize.verify');
    });
