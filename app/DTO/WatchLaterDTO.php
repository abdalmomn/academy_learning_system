<?php

namespace App\DTO;

class WatchLaterDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $video_id,
    ) {}
}
