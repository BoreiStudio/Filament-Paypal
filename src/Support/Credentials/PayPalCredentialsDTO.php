<?php

namespace BoreiStudio\FilamentPayPal\Support\Credentials;

use BoreiStudio\FilamentPayPal\Contracts\PayPalCredentials;

class PayPalCredentialsDTO implements PayPalCredentials
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly bool $sandbox = true,
    ) {}

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }
}
