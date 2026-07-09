<?php

namespace App\Policies;

use App\Models\Screen;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ScreenPolicy
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
    public function view(User $user, Screen $screen): bool
    {
        return $this->isOwnerOf($screen, $user);
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
    public function update(User $user, Screen $screen): bool
    {
        return $this->isOwnerOf($screen, $user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Screen $screen): bool
    {
        return $this->isOwnerOf($screen, $user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Screen $screen): bool
    {
        return $this->isOwnerOf($screen, $user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Screen $screen): bool
    {
        return $this->isOwnerOf($screen, $user);
    }

    private function isOwnerOf(Screen $screen, User $user) : bool {
        return $screen->theater->owners
            ->where('id', $user->id)
            ->isNotEmpty();
    }
}
