<?php
namespace App\Repositories\Contracts;

use App\Models\MovieShow;
use Illuminate\Pagination\LengthAwarePaginator;

interface MovieShowRepositoryInterface{
    public function all(int $perPage = 20): LengthAwarePaginator;

    public function find(int $id): MovieShow;

    public function create(array $data): MovieShow;

    public function update(MovieShow $movieShow, array $data): MovieShow;

    public function delete(MovieShow $movieShow): bool;
}
