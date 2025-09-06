<?php

namespace App\Events\Notification;
use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ?User $user,
        public ?Course $course,
        public float $amount,
        public string $method,
        public ?string $reason = null
    ) {}
}
