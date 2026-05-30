<?php
namespace App\Repositories;

use App\Models\MovieShow;
use App\Repositories\Contracts\MovieShowRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class MovieShowRepository implements MovieShowRepositoryInterface
{
    public function all(int $perPage = 20): LengthAwarePaginator {
        return MovieShow::latest() 
            ->paginate($perPage);
    }

    public function find(int $id): MovieShow {
        return MovieShow::findOrFail($id);
    }

    public function create(array $data): MovieShow {
        return MovieShow::create($data);
    }

    public function update(MovieShow $movieShow, array $data): MovieShow {
        $movieShow->update($data);
        return $movieShow->fresh();
    }

    public function delete(MovieShow $movieShow): bool {
        return $movieShow->delete();
    }
}

