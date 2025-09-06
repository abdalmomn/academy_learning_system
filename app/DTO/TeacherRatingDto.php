<?php

namespace App\DTO;

class TeacherRatingDto
{
    public int $id;
    public float $rate;
    public int $teacher_id;
    public int $user_id;
    public string $created_at;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id         = $data['id'];
        $dto->rate       = $data['rate'];
        $dto->teacher_id = $data['teacher_id'];
        $dto->user_id    = $data['user_id'];
        $dto->created_at = $data['created_at'];

        return $dto;
    }
}
