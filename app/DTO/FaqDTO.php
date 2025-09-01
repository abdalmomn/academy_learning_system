<?php

namespace App\DTO;

class FaqDTO
{
    public function __construct(
        public readonly string $question,
        public readonly string $answer,
        public readonly int $faq_category_id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['question'],
            $data['answer'],
            $data['faq_category_id']
        );
    }

}
