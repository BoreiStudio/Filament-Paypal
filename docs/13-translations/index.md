# Translations

The plugin includes translations for English and Spanish.

## Available Languages

| Language | Locale | File |
|---|---|---|
| English | `en` | `resources/lang/en/messages.php` |
| Spanish | `es` | `resources/lang/es/messages.php` |

## Customizing Translations

Publish the language files:

```bash
php artisan vendor:publish --provider="BoreiStudio\FilamentPayPal\PayPalServiceProvider" --tag="filament-paypal-translations"
```

Then edit the files in `resources/lang/vendor/filament-paypal/`.

## Adding a New Language

1. Copy `resources/lang/en/messages.php` to `resources/lang/{locale}/messages.php`
2. Translate the values
3. Set your app's locale: `config('app.locale')`
