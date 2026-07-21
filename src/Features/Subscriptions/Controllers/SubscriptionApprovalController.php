<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Controllers;

use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubscriptionApprovalController extends Controller
{
    public function approval(Request $request)
    {
        $subscriptionId = $request->query('subscription_id');

        if (! $subscriptionId) {
            return view('filament-paypal::subscription.result', [
                'success' => false,
                'message' => __('filament-paypal::messages.subscriptions.no_subscription_id'),
            ]);
        }

        $subscription = Subscription::where('paypal_subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            return view('filament-paypal::subscription.result', [
                'success' => false,
                'message' => __('filament-paypal::messages.subscriptions.not_found'),
            ]);
        }

        if ($subscription->status->value === 'APPROVAL_PENDING') {
            $subscription->update(['status' => 'APPROVED']);
        }

        return view('filament-paypal::subscription.result', [
            'success' => true,
            'message' => __('filament-paypal::messages.subscriptions.approved'),
            'subscription' => $subscription->fresh(),
        ]);
    }

    public function cancel(Request $request)
    {
        return view('filament-paypal::subscription.result', [
            'success' => false,
            'message' => __('filament-paypal::messages.subscriptions.cancelled_by_user'),
        ]);
    }
}
