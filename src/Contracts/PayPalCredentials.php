<?php

namespace BoreiStudio\FilamentPayPal\Contracts;

interface PayPalCredentials
{
    public function getClientId(): string;

    public function getClientSecret(): string;

    public function isSandbox(): bool;
}
