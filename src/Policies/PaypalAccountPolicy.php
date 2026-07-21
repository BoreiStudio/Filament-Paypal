<?php

namespace BoreiStudio\FilamentPayPal\Policies;

use Illuminate\Database\Eloquent\Model;

class PaypalAccountPolicy
{
    public function viewAny(Model $user): bool
    {
        return true;
    }

    public function view(Model $user, $account): bool
    {
        return true;
    }

    public function create(Model $user): bool
    {
        return true;
    }

    public function update(Model $user, $account): bool
    {
        return true;
    }

    public function delete(Model $user, $account): bool
    {
        return true;
    }
}
