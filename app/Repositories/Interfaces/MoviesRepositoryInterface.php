<?php
namespace App\Repositories\Interfaces;

interface MoviesRepositoryInterface{
    public function getById(string $id);
    public function getLatestRelease(string $language);
    public function getUpcoming(string $language);
    // public function getNowPlaying();
}