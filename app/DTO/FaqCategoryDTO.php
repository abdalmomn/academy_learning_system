<?php

namespace App\DTO;

class FaqCategoryDTO
{
    public function __construct(
        public readonly string $faq_category_name
    ) {}

    public static function fromArray(array $data): self
    {
        return new self($data['faq_category_name']);
    }
}
