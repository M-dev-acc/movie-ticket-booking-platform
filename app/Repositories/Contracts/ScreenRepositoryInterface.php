<?php
namespace App\Repositories\Contracts;

use App\Models\Screen;
use Illuminate\Pagination\LengthAwarePaginator;

interface ScreenRepositoryInterface{
    public function all(int $perPage = 20, ?int $theaterId = null): LengthAwarePaginator;

    public function find(int $id): Screen;

    public function create(array $data) : Screen;

    public function update(Screen $screen, array $data) : Screen;

    public function delete(Screen $screen): ?bool;
}
