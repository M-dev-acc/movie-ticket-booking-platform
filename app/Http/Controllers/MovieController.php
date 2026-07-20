<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Services\ExternalApi\Contracts\MovieApiInterface;
use App\Services\MovieService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly MovieService $service,
        private readonly MovieApiInterface $api,
    ) {}

    /**
     * GET /movies/latest/{page?}
     * Returns latest released movies from DB.
     */
    public function index(): JsonResponse
    {
        $movies = $this->service->getLatest();

        return $this->paginated(
            paginator: $movies,
            message: 'Latest movies',
            resourceClass: MovieResource::class);
    }

    /**
     * GET /movies/{id}
     * Returns details of a single movie by TMDB external ID.
     * Returns 404 if not found in DB and API is unreachable.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $movie = $this->service->getMovie((string) $id);
            return $this->success(data: $movie, message: 'Movie details');

        } catch (\RuntimeException $e) {
            // Thrown by MovieService when movie is missing from DB and API is unreachable
            return $this->notFound('Movie not found.');
        }
    }

    /**
     * GET /movies/upcoming/{page?}
     * Returns upcoming movies — DB first, API fallback.
     */
    public function upcoming(): JsonResponse
    {
        $movies = $this->service->getUpcoming();

        return $this->paginated(
            paginator: $movies,
            message: 'Upcoming movies',
            resourceClass: MovieResource::class);
    }

    /**
     * GET /movies/search?query=...
     * Searches movies from DB by title or overview.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:1', 'max:255', 'regex:/^[A-Za-z0-9() ]+$/s'],
        ], [
            'query.required' => 'Please enter a valid search input.',
            'query.regex'    => 'Please enter a valid search input.',
            'query.max'      => 'Search input must be under 255 characters.',
        ]);

        $movies = $this->service->searchMovies($validated['query']);

        return $this->paginated($movies, 'Search results');
    }
}
