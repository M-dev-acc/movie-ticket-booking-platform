<?php

namespace App\Repositories;

use App\Models\Theater;
use App\Repositories\Interfaces\TheaterRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TheaterRepository implements TheaterRepositoryInterface
{
    public function all(int $page) {
        $perPage = 10;
        $columns = ['*'];
        return Theater::paginate($perPage, ['*'], 'page', $page);
    }

    public function find(int $id): Theater {
        return Theater::findOrFail($id);
    }

    public function create(array $data): Theater {
        return Theater::create($data);
    }

    public function update(int $id, array $data) : Theater {
        $theater = Theater::findOrFail($id);
        $theater->update($data);

        return $theater;
    }

    public function delete(int $id): bool {
        $theater = Theater::findOrFail($id);
        return $theater->delete();
    }
}
