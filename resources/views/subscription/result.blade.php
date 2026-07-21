<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $success ? __('filament-paypal::messages.subscriptions.success_title') : __('filament-paypal::messages.subscriptions.failed_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-md p-8 w-full max-w-md text-center">
        @if ($success)
            <div class="text-green-500 text-6xl mb-4">&#10004;</div>
            <h1 class="text-2xl font-bold text-green-700 mb-4">{{ __('filament-paypal::messages.subscriptions.success_title') }}</h1>
            <p class="text-gray-600 mb-6">{{ $message }}</p>
            @if (isset($subscription))
                <div class="bg-gray-50 rounded p-4 mb-6 text-left">
                    <p class="text-sm text-gray-500">{{ __('filament-paypal::messages.subscriptions.paypal_subscription_id') }}: <strong>{{ $subscription->paypal_subscription_id }}</strong></p>
                    <p class="text-sm text-gray-500">{{ __('filament-paypal::messages.subscriptions.subscriber_email') }}: <strong>{{ $subscription->subscriber_email }}</strong></p>
                    <p class="text-sm text-gray-500">{{ __('filament-paypal::messages.subscriptions.status') }}: <strong>{{ $subscription->status->value }}</strong></p>
                </div>
            @endif
        @else
            <div class="text-red-500 text-6xl mb-4">&#10008;</div>
            <h1 class="text-2xl font-bold text-red-700 mb-4">{{ __('filament-paypal::messages.subscriptions.failed_title') }}</h1>
            <p class="text-gray-600 mb-6">{{ $message }}</p>
        @endif
    </div>
</body>
</html>
