<?php

namespace App\DTO;

class VideoDto
{

    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $poster,
        public readonly string $url,
        public readonly int $course_id,
        public readonly ?string $duration = null
    ){}

    public static function fromArray($data)
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            poster: $data['poster'] ?? null,
            url: $data['url'],
            course_id: $data['course_id'],
            duration: $data['duration'] ?? null
        );
    }
}
