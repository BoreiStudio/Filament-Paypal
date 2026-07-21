<?php

namespace BoreiStudio\FilamentPayPal\Support\Credentials;

use Exception;

class PayPalAccountNotConnectedException extends Exception
{
    public function __construct()
    {
        parent::__construct('No PayPal account is connected.');
    }
}
