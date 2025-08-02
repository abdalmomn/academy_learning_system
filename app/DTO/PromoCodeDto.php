<?php

namespace App\DTO;

use Carbon\Carbon;

class PromoCodeDto
{
    public function __construct(
        public readonly int $discount_percentage,
        public readonly int $teacher_id,
        public readonly int $usage_limit,
        public readonly ?Carbon $expires_in = null
    ){}

    public static function fromArray($data)
    {
        return new self(
            discount_percentage: $data['discount_percentage'],
            teacher_id: $data['teacher_id'],
            usage_limit: $data['usage_limit'] ?? 0,
            expires_in: isset($data['expires_in']) ? Carbon::parse($data['expires_in']) : null
        );
    }
}

