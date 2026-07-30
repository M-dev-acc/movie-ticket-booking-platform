<?php

namespace App\Jobs\ShowSeat;

use App\Models\MovieShow;
use App\Models\ShowSeat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UnlockShowSeatsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120]; // seconds between retries



    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    public function uniqueId(): string
    {
        return now()->format('Y-m-d H:i:s');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lockedSeat = ShowSeat::select(['id', 'show_id', 'seat_id', 'locked_until'])
            ->locked()
            ->get();


    }

    public function failed(\Throwable $exception): void
    {
        Log::error('UnlockShowSeatsJob: job failed permanently.', [
            'language' => $this->uniqueId(),
            'error'    => $exception->getMessage(),
        ]);
    }
}
