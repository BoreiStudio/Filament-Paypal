<?php

namespace BoreiStudio\FilamentPayPal\Features\Checkout\Controllers;

use BoreiStudio\FilamentPayPal\Features\Orders\Actions\CaptureOrderAction;
use BoreiStudio\FilamentPayPal\Features\Orders\Actions\CreateOrderAction;
use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CheckoutController extends Controller
{
    public function form()
    {
        return view('filament-paypal::checkout.form');
    }

    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency_code' => 'required|string|size:3',
            'description' => 'nullable|string|max:255',
        ]);

        $account = PaypalAccount::where('status', 'connected')->first();

        if (! $account) {
            return back()->withErrors(['error' => __('filament-paypal::messages.checkout.no_account')]);
        }

        $order = app(CreateOrderAction::class)->execute([
            'account_id' => $account->id,
            'amount' => $request->amount,
            'currency_code' => strtoupper($request->currency_code),
            'description' => $request->description,
            'source' => 'checkout',
        ]);

        if (! $order->approval_url) {
            return back()->withErrors(['error' => __('filament-paypal::messages.checkout.no_approval_url')]);
        }

        return redirect($order->approval_url);
    }

    public function return(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            return view('filament-paypal::checkout.result', ['success' => false, 'message' => __('filament-paypal::messages.checkout.no_token')]);
        }

        $order = Order::where('paypal_order_id', $token)->first();

        if (! $order) {
            return view('filament-paypal::checkout.result', ['success' => false, 'message' => __('filament-paypal::messages.checkout.order_not_found')]);
        }

        try {
            app(CaptureOrderAction::class)->execute($order);

            return view('filament-paypal::checkout.result', [
                'success' => true,
                'message' => __('filament-paypal::messages.checkout.success'),
                'order' => $order->fresh(),
            ]);
        } catch (\Throwable $e) {
            return view('filament-paypal::checkout.result', [
                'success' => false,
                'message' => __('filament-paypal::messages.checkout.capture_failed').' '.$e->getMessage(),
            ]);
        }
    }
}
