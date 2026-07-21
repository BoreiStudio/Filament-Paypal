<?php

namespace BoreiStudio\FilamentPayPal\Policies;

use Illuminate\Database\Eloquent\Model;

class PaypalPayoutPolicy
{
    public function viewAny(Model $user): bool
    {
        return true;
    }

    public function view(Model $user, $payout): bool
    {
        return true;
    }

    public function create(Model $user): bool
    {
        return true;
    }

    public function update(Model $user, $payout): bool
    {
        return false;
    }

    public function delete(Model $user, $payout): bool
    {
        return false;
    }
}
