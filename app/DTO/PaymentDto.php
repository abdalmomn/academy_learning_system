<?php

namespace App\DTO;

class PaymentDto
{
    public function __construct(
        public readonly ?string $promo_code,
        public readonly string $payment_method,
        public readonly int $course_id
    ){}

    public static function fromArray($data)
    {
        return new self(
            promo_code: $data['promo_code'] ?? null,
            payment_method: $data['payment_method'],
            course_id: $data['course_id']
        );
    }
}
