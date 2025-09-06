<?php

namespace App\DTO;

class StrikeDto
{
    public int $id;
    public int $user_id;
    public string $date;
    public string $watch_time;
    public bool $attended;
    public int $streak;
    public string $created_at;
    public string $updated_at;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id         = $data['id'];
        $dto->date     = $data['date'];
        $dto->user_id    = $data['user_id'];
        $dto->watch_time = $data['watch_time'];
        $dto->attended = $data['attended'];
        $dto->streak = $data['streak'];
        $dto->created_at = $data['created_at'];
        $dto->updated_at = $data['updated_at'];
        return $dto;
    }
}
