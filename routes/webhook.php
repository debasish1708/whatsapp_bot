<?php

use App\Http\Controllers\Webhook\StripeWebhookController;
use App\Http\Controllers\Webhook\ThreeSixtyDialogWebhookController;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Controllers\WebhookController;

Route::post('whatsapp-message', ThreeSixtyDialogWebhookController::class)->name('sendWhatsAppMessage');

Route::post('/stripe/webhook',[StripeWebhookController::class, 'handleWebhook']);
