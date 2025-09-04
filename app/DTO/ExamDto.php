<?php

namespace App\DTO;

use Carbon\Carbon;

class ExamDto
{
    public function __construct(
        public readonly string $type,
        public readonly string $exam_mode,
        public readonly string $title,
        public readonly string $description,
        public readonly Carbon $start_date,
        public readonly Carbon $end_date,
        public readonly bool $is_mandatory,
        public readonly ?int $course_id,
        public readonly ?int $video_id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            exam_mode: $data['exam_mode'],
            title: $data['title'],
            description: $data['description'],
            start_date: Carbon::parse($data['start_date']),
            end_date: Carbon::parse($data['end_date']),
            is_mandatory: (bool) $data['is_mandatory'],
            course_id: $data['course_id'] ?? null,
            video_id: $data['video_id'] ?? null
        );
    }
}
