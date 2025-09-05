<?php

namespace App\DTO;

class InterestDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $category_id,
    ) {}

}
