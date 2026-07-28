<?php

namespace App\Jobs;

use App\Models\MovieShow;
use App\Models\Seat;
use App\Models\ShowSeat;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PopulateShowSeatsJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public MovieShow $show
    ) {
    }

    public function unique(): string {
        return $this->show->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $seats = Seat::select(['id', 'type'])
            ->where('screen_id', $this->show->screen->id)
            ->where('is_active', true)
            ->get();
        if ($seats->isEmpty()) {
            Log::warning('This screen does not have any seats assigned.');
            return;
        }

        $groups = $seats->mapToGroups(function (array $seat, int $key) {
            return [
                $seat['type'] => [
                    'show_id' => $this->show->id,
                    'seat_id' => $seat['id'],
                    'status' => 'available',
                    'price' => $this->show->price,
                ]
            ];
        });

        foreach ($groups as $type => $group) {
            ShowSeat::insertOrIgnore($group->toArray());
        }
    }
}
