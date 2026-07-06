<?php

namespace App\Services\Theater;

use App\Models\Theater;
use App\Models\User;
use InvalidArgumentException;

class TheaterOwnerService
{
    public function assign(Theater $theater, int $userId) : void {
        $this->validateUserRelations($theater, $userId);

        $theater->owners()->attach($userId, [
            'assigned_by' => auth()->id(),
        ]);

    }

    public function revoke(Theater $theater, int $userId) : void {
        $theater->owners()->detach($userId);
    }

    private function validateUserRelations(Theater $theater, int $userId) : void {
        $user = User::findOrFail($userId);
        if (!$user->hasRole('owner')) {
            throw new InvalidArgumentException("User does not have the owner role.");
        }

        $alreadyAssignedRole = $theater->owners()
            ->where('user_id', $userId)
            ->exists();
        if ($alreadyAssignedRole) {
            throw new InvalidArgumentException("User is already an owner of this theater.");
        }
    }
}
