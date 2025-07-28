<?php

namespace App\Repositories\Interfaces;

interface TheaterRepositoryInterface {
    public function all(int $id);
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
