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
        LengthAwarePaginator|ResourceCollection $paginator,
        string $message,
        int $statusCode = 200
    ) : JsonResponse {
        if ($paginator instanceof ResourceCollection) {
            $resource = $paginator->resource;
            $items = $paginator->resource()->getData(true)['data'];
        } else {
            $resource = $paginator;
            $items = $paginator->items();
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $items,
            'meta' => [
                'current_page' => $resource->currentPage(),
                'last_page' => $resource->lastPage(),
                'per_page' => $resource->perPage(),
                'total' => $resource->total(),
                'from' => $resource->firstItem(),
                'to' => $resource->lastItem()
            ],
            'links' => [
                'first' => $resource->url(1),
                'last' => $resource->url($resource->lastPage()),
                'prev' => $resource->previousPageUrl(),
                'next' => $resource->nextPageUrl()
            ]
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

