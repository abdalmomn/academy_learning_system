<?php

namespace App\DTO;

class AchievementDTO
{
    public string $title;
    public string $description;
    public ?string $icon_path = null;
    public int $user_id;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->title = $data['title'];
        $dto->description = $data['description'];
        $dto->icon_path = $data['icon_path'] ?? null;
        return $dto;
    }
}
