<?php

namespace BoreiStudio\FilamentPayPal\Policies;

use Illuminate\Database\Eloquent\Model;

class PaypalSubscriptionPolicy
{
    public function viewAny(Model $user): bool
    {
        return true;
    }

    public function view(Model $user, $subscription): bool
    {
        return true;
    }

    public function create(Model $user): bool
    {
        return true;
    }

    public function update(Model $user, $subscription): bool
    {
        return false;
    }

    public function delete(Model $user, $subscription): bool
    {
        return false;
    }
}
