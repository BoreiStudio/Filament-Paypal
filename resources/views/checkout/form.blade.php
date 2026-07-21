<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('filament-paypal::messages.checkout.title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-md p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">{{ __('filament-paypal::messages.checkout.title') }}</h1>

        @if (isset($errors) && $errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('paypal.checkout.create') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('filament-paypal::messages.checkout.amount') }}
                </label>
                <div class="flex gap-2">
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           class="flex-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('amount') }}">
                    <select name="currency_code"
                            class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="USD" {{ old('currency_code') === 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('currency_code') === 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="GBP" {{ old('currency_code') === 'GBP' ? 'selected' : '' }}>GBP</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('filament-paypal::messages.checkout.description') }}
                </label>
                <input type="text" name="description"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="{{ old('description') }}" placeholder="{{ __('filament-paypal::messages.checkout.description_placeholder') }}">
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                {{ __('filament-paypal::messages.checkout.pay_button') }}
            </button>
        </form>
    </div>
</body>
</html>
