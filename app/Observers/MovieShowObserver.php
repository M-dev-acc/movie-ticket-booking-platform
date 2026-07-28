<?php

namespace App\Observers;

use App\Models\MovieShow;

class MovieShowObserver
{
    /**
     * Handle the MovieShow "created" event.
     */
    public function created(MovieShow $movieShow): void
    {
        //
    }

    /**
     * Handle the MovieShow "updated" event.
     */
    public function updated(MovieShow $movieShow): void
    {
        //
    }

    /**
     * Handle the MovieShow "deleted" event.
     */
    public function deleted(MovieShow $movieShow): void
    {
        //
    }

    /**
     * Handle the MovieShow "restored" event.
     */
    public function restored(MovieShow $movieShow): void
    {
        //
    }

    /**
     * Handle the MovieShow "force deleted" event.
     */
    public function forceDeleted(MovieShow $movieShow): void
    {
        //
    }
}
