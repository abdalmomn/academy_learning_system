<?php

namespace App\DTO;

class UpdateOptionDto
{
    public function __construct(
        public string $option_text,
        public bool $is_correct
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            option_text: $data['option_text'],
            is_correct: $data['is_correct']
        );
    }
}
