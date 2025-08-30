<?php
namespace App\Repositories\Interfaces;

interface MoviesRepositoryInterface{
    public function getById(string $id);
    public function getLatestRelease(string $language, int $page);
    public function getUpcoming(string $language, int $page);
    // public function getNowPlaying();
}