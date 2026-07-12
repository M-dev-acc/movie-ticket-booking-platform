<?php

namespace App\Policies;

use App\Models\Seat;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SeatPolicy
{
    /**
     * Admin bypasses all methods below
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('owner');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Seat $seat): bool
    {
        return $this->isOwnerOf($seat, $user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('owner');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Seat $seat): bool
    {
        return $this->isOwnerOf($seat, $user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Seat $seat): bool
    {
        return $this->isOwnerOf($seat, $user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Seat $seat): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Seat $seat): bool
    {
        return false;
    }

    private function isOwnerOf(Seat $seat, User $user) : bool {
        return $seat->screen
            ?->theater
            ?->owners()
            ?->wherePivot('user_id', $user->id)
            ?->exists();
    }
}
