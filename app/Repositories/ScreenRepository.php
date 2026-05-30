<?php
namespace App\Repositories;

use App\Models\Screen;
use App\Repositories\Contracts\ScreenRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ScreenRepository implements ScreenRepositoryInterface
{
    public function all(int $perPage = 20, ?int $theaterId = null): LengthAwarePaginator
    {
        return Screen::latest()
            ->when($theaterId, fn($query) => $query->where('theater_id', $theaterId))
            ->paginate($perPage);
    }

    public function find(int $id) : Screen
    {
        return Screen::findOrFail($id);
    }

    public function create(array $data) : Screen
    {
        return Screen::create($data);
    }

    public function update(Screen $screen, array $data) : Screen
    {
        $screen->update($data);

        return $screen->fresh();
    }

    public function delete(Screen $screen): ?bool
    {
        return $screen->delete();
    }
}

