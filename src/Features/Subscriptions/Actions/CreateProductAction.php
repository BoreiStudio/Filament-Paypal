<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Actions;

use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Product;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class CreateProductAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(array $data): Product
    {
        $http = $this->client->http();

        $response = $http->post('/v1/catalogs/products', [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? 'SERVICE',
            'category' => $data['category'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'home_url' => $data['home_url'] ?? null,
        ]);

        $response->throw();
        $result = $response->json();

        throw_if(empty($result['id']), new \RuntimeException(
            'PayPal API returned unexpected response: ' . json_encode($result)
        ));

        return Product::create([
            'account_id' => $data['account_id'],
            'paypal_product_id' => $result['id'],
            'name' => $result['name'] ?? $data['name'],
            'description' => $result['description'] ?? $data['description'] ?? null,
            'type' => $result['type'] ?? $data['type'] ?? 'SERVICE',
            'category' => $result['category'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'home_url' => $data['home_url'] ?? null,
            'status' => $result['status'] ?? 'CREATED',
            'paypal_response' => $result,
        ]);
    }
}
