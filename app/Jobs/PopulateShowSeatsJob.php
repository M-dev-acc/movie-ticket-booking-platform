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

    public int $tries = 3;
    public array $backoff = [30, 60, 120]; // seconds between retries

    /**
     * Create a new job instance.
     */
    public function __construct(
        public MovieShow $show
    ) {
    }

    public function uniqueId(): string {
        return $this->show->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $seats = Seat::select(['id', 'type'])
            ->where('screen_id', $this->show->screen_id)
            ->where('is_active', true)
            ->get();
        if ($seats->isEmpty()) {
            Log::warning('PopulateShowSeatsJob: screen has no active seats.', [
                'show_id' => $this->show->id,
                'screen_id' => $this->show->screen_id,
            ]);
            return;
        }

        $groups = $seats->mapToGroups(function (Seat $seat) {
            return [
                $seat['type'] => [
                    'show_id' => $this->show->id,
                    'seat_id' => $seat['id'],
                    'status' => 'available',
                    'price' => $this->show->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ];
        });

        foreach ($groups as $type => $group) {
            ShowSeat::insertOrIgnore($group->toArray());
            Log::info("The $type seats populate successfully.");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('PopulateShowSeatsJob: job failed permanently.', [
            'language' => $this->show->id,
            'error'    => $exception->getMessage(),
        ]);
    }
}
