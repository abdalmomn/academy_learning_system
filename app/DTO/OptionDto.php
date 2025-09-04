<?php

namespace App\DTO;

class OptionDto
{
    public function __construct(
        public string $option_text,
        public bool $is_correct,
        public ?int $question_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            option_text: $data['option_text'],
            is_correct: $data['is_correct'],
            question_id: $data['question_id']
        );
    }
}
