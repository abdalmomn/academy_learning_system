<?php

namespace App\DTO;

class QuestionDto
{
    public function __construct(
        public readonly string $question_text,
        public readonly string $question_type, // mcq | project
        public readonly int $mark,
        public readonly ?int $exam_id = null // nullable + optional
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            question_text: $data['question_text'],
            question_type: $data['question_type'],
            mark: $data['mark'],
            exam_id: $data['exam_id'] ?? null
        );
    }

    /**
     * Convert to array without null values (useful for update).
     */
    public function toArray(): array
    {
        return array_filter((array)$this, fn($value) => !is_null($value));
    }
}
