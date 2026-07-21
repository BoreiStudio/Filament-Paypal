<?php

namespace BoreiStudio\FilamentPayPal\Contracts;

use BoreiStudio\FilamentPayPal\Support\Credentials\PayPalCredentialsDTO;

interface CredentialResolverInterface
{
    public function resolve(): PayPalCredentialsDTO;

    public function applicationCredentials(): array;
}
