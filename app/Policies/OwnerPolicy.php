<?php

namespace App\Policies;

use App\Models\User;

abstract class OwnerPolicy
{
    protected function ownsCurrentTheater(User $user)
    {
        $theater = request()->route('theater');

        return $user->ownedTheaters()
            ->whereKey($theater)
            ->exists();
    }
}
