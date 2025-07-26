<?php

namespace App\DTO;

class SignUpDto
{
    public function __construct(
        public readonly string $username,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $social_id,
        public readonly ?string $social_type,
        public readonly ?string $user_type,
        public bool $is_approved = false,
        public readonly ?array $file_path,
        public readonly ?string $description,
        public readonly ?string $family_email = null,
        public readonly ?string $user_type
    )
    {}


    public static function fromArray(array $data){
        return new self(
            username: $data['username'],
            email: $data['email'],
            password: $data['password'],
            social_id: $data['social_id'] ?? null,
            social_type: $data['social_type'] ?? null,
            user_type: $data['user_type'],
            is_approved: $data['is_approved'] ?? false,
            file_path: $data['file_path'] ?? [],
            description: $data['description'] ?? null,
            user_type: $data['user_type']
        );
    }
}
