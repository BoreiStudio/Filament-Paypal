<?php

namespace BoreiStudio\FilamentPayPal\Policies;

use Illuminate\Database\Eloquent\Model;

class PaypalProductPolicy
{
    public function viewAny(Model $user): bool
    {
        return true;
    }

    public function view(Model $user, $product): bool
    {
        return true;
    }

    public function create(Model $user): bool
    {
        return true;
    }

    public function update(Model $user, $product): bool
    {
        return true;
    }

    public function delete(Model $user, $product): bool
    {
        return true;
    }
}
