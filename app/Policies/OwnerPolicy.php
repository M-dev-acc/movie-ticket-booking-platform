<?php

namespace App\Policies;

use App\Models\Theater;
use App\Models\User;

abstract class OwnerPolicy
{
    protected function ownsCurrentTheater(User $user, Theater $theater): bool
    {
        return $theater->owners()
            ->whereKey($user->id)
            ->exists();
    }
}
