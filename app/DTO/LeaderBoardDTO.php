<?php

namespace App\DTO;

class LeaderBoardDTO
{
    public int $leader_id;
    public string $leader_type;
    public int $points;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->leader_id   = $data['leader_id'];
        $dto->leader_type = $data['leader_type'];
        $dto->points      = $data['points'] ?? 0;
        return $dto;
    }

}
