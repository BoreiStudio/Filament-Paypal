<?php

namespace BoreiStudio\FilamentPayPal\Support\Http;

use Exception;

class PayPalException extends Exception
{
    public function __construct(
        string $message = '',
        private readonly ?array $paypalResponse = null,
    ) {
        parent::__construct($message);
    }

    public function getPayPalResponse(): ?array
    {
        return $this->paypalResponse;
    }
}
