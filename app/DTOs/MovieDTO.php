<?php
namespace App\DTOs;

use Carbon\Carbon;

readonly class MovieDTO
{
    public function __construct(
        public string $externalId,
        public string $title,
        public ?string $posterPath,
        public ?Carbon $releaseDate,
        public array $genres,
        public ?float $rating,
        public ?string $originalLanguage,
        public ?string $overview,

        // public ?string $originalTitle,
        // public ?string $backdropPath,
        // public ?int $voteCount,
        // public ?float $popularity,
        // public ?bool $adult,
    ) {}

    public static function fromTmdb(array $data) : self {
        return new self(
            externalId: (string) $data['id'],
            title: ($data['title']),
            posterPath: $data['poster_path'] ?? null,
            releaseDate: self::parseDate($data['release_date'] ?? null),
            genres: $data['genre_ids'] ?? [],
            rating: isset($data['vote_average']) ? (float) $data['vote_average'] : null,
            originalLanguage: $data['original_language'] ?? null,
            overview: $data['overview'] ?? null,

            // originalTitle: $data['original_title'] ?? null,
            // backdropPath: $data['backdrop_path'] ?? null,
            // voteCount: isset($data['vote_count']) ? (int) $data['vote_count'] : null,
            // popularity: isset($data['popularity']) ? (float) $data['popularity'] : null,
            // adult: isset($data['adult']) ? (bool) $data['adult'] : null,
        );
    }

    public function toArray() : array {
        return [
            'external_id' => $this->externalId,
            'title' => $this->title,
            'poster_path' => $this->posterPath,
            'release_date' => $this->releaseDate?->toDateString(),
            'genres' => $this->genres,
            'rating' => $this->rating,
            'original_language' => $this->originalLanguage,
            'overview' => $this->overview,

            // 'synced_at' => now(),

            // 'original_title' => $this->originalTitle,
            // 'backdrop_path' => $this->backdropPath,
            // 'vote_count' => $this->voteCount,
            // 'popularity' => $this->popularity,
            // 'adult' => $this->adult,
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

