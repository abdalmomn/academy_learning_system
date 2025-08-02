<?php

namespace App\DTO;

class ApproveCourseDto
{
    public function __construct(
     public readonly string $status
    )
    {}

    public static function fromArray(array $data)
    {
        return new self(
            status: $data['status']
        );
    }
}
