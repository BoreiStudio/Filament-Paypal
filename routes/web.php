<?php

use BoreiStudio\FilamentPayPal\Features\Checkout\Controllers\CheckoutController;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/paypal/webhooks/{account?}', [WebhookController::class, '__invoke'])
    ->name('paypal.webhooks')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

Route::prefix('paypal/checkout')->name('paypal.checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'form'])->name('form');
    Route::post('/', [CheckoutController::class, 'create'])->name('create');
    Route::get('/return', [CheckoutController::class, 'return'])->name('return');
});

Route::prefix('paypal/subscription')->name('paypal.subscription.')->group(function () {
    Route::get('/approval', [\BoreiStudio\FilamentPayPal\Features\Subscriptions\Controllers\SubscriptionApprovalController::class, 'approval'])->name('approval');
    Route::get('/cancel', [\BoreiStudio\FilamentPayPal\Features\Subscriptions\Controllers\SubscriptionApprovalController::class, 'cancel'])->name('cancel');
});
