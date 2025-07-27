<?php

namespace App\DTO;

class CategoryDto
{
    public function __construct(
        public readonly string $category_name
    ) {}


    public static function fromArray(array $data)
    {
        return new self(
            category_name:  $data['category_name']
        );
    }
}
