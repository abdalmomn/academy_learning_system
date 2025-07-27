<?php

namespace App\DTO;

use Carbon\Carbon;

class ProfileDto
{
    public function __construct(
        public readonly ?string $first_name,
        public readonly ?string $last_name,
        public readonly ?Carbon $date_of_birth,
        public readonly ?string $phone_number,
        public readonly ?string $profile_photo
    )
    {}

    public static function fromArray($data)
    {
        return new self(
            first_name: $data['first_name'] ?? null,
            last_name: $data['last_name'] ?? null,
            date_of_birth: isset($data['date_of_birth']) ? Carbon::parse($data['date_of_birth']) : null,
            phone_number: $data['phone_number'] ?? null,
            profile_photo: $data['profile_photo'] ?? null
        );
    }
}
