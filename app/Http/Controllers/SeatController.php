<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieShowSeatResource;
use App\Models\MovieShow;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    use ApiResponse;

    public function index(MovieShow $movieShow): JsonResponse
    {
        return $this->paginated(
            paginator: $movieShow->seats()
                ->paginate(),
            message: "Show's seat",
            resourceClass: MovieShowSeatResource::class,
        );
    }
}
