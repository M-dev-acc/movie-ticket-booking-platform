<?php
namespace App\DTOs;

use Carbon\Carbon;

readonly class MovieDTO
{
    public function __construct(
        public string $externalId,
        public string $title,
        public ?string $originalTitle,
        public ?string $overview,
        public ?string $posterPath,
        public ?string $backdropPath,
        public ?string $originalLanguage,
        public ?float $voteAverage,
        public ?int $voteCount,
        public ?float $popularity,
        public ?bool $adult,
        public ?Carbon $releaseDate,
    ) {}

    public static function fromTmdb(array $data) : self {
        return new self(
            externalId: (string) $data['id'],
            title: $data['title'],
            originalTitle: $data['original_title'] ?? null,
            overview: $data['overview'] ?? null,
            posterPath: $data['poster_ path'] ?? null,
            backdropPath: $data['backdrop_path'] ?? null,
            originalLanguage: $data['original_language'] ?? null,
            voteAverage: isset($data['vote_average']) ? (float) $data['vote_average'] : null,
            voteCount: isset($data['vote_count']) ? (int) $data['vote_count'] : null,
            popularity: isset($data['popularity']) ? (float) $data['popularity'] : null,
            adult: isset($data['adult']) ? (bool) $data['adult'] : null,
            releaseDate: self::parseDate($data['release_date'] ?? null),
        );
    }

    public function toArray() : array {
        return [
            'external_id' => $this->externalId,
            'title' => $this->title,
            'original_title' => $this->originalTitle,
            'overview' => $this->overview,
            'poster_path' => $this->posterPath,
            'backdrop_path' => $this->backdropPath,
            'original_language' => $this->originalLanguage,
            'vote_average' => $this->voteAverage,
            'vote_count' => $this->voteCount,
            'popularity' => $this->popularity,
            'adult' => $this->adult,
            'release_date' => $this->releaseDate?->toDateString(),
            'synced_at' => now(),
        ];
    }

    private static function parseDate(?string $date) : ?Carbon {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date);
        } catch (\Exception) {
            return null;
        }
    }
}

