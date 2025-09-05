<?php

namespace App\DTO;

class CommentDto
{
    public function __construct(
        public readonly string $comment,
        public readonly int $user_id,
        public readonly int $video_id
    ) {}

    public static function fromArray(array $data)
    {
        return new self(
            comment:  $data['comment'],
            user_id:  auth()->id(),
            video_id:  $data['video_id']
        );
    }
}
