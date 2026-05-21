<?php
namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    /**
     * Returns a standardized success response
     */
    protected function success(
        mixed $data,
        int $statusCode = 200,
        string $message = "Success",
        array $headers = []
    ) : JsonResponse {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode, $headers);
    }

    /**
     * Returns a standardized 'paginate' response
     */
    protected function paginated(
        LengthAwarePaginator $paginator,
        string $message = 'Success',
        int $statusCode = 200,
        ?string $resourceClass = null,
    ): JsonResponse {
        // Transform items through the JsonResource if provided,
        // otherwise return raw paginator items.
        $items = $resourceClass
            ? $resourceClass::collection($paginator->items())->resolve()
            : $paginator->items();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $items,
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
            'links'   => [
                'first' => $paginator->url(1),
                'last'  => $paginator->url($paginator->lastPage()),
                'prev'  => $paginator->previousPageUrl(),
                'next'  => $paginator->nextPageUrl(),
            ],
        ], $statusCode);
    }

    /**
     * Returns a standarized 'created' response
     */
    protected function created(
        mixed $data = null,
        string $message = "Resource created successfully."
    ) : JsonResponse {
        return $this->success($data, 201, $message);
    }

    /**
     * Returns a standarized 'no content' response
     */
    protected function noContent(string $message) : JsonResponse {
        return $this->success(null, 204, $message);
    }

    /**
     * Returns a standarized error response
     */
    protected function error(
        int $statusCode = 400,
        string $message = "An error occured",
        mixed $errors = null,
        array $headers = []
    ) : JsonResponse {
        $response = [
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ];
        return response()->json($response, $statusCode, $headers);
    }

    /**
     * Returns a standardized 'not found' error response
     */
    protected function notFound(string $message = "Resource not found") : JsonResponse {
        return $this->error(404, $message);
    }
}

