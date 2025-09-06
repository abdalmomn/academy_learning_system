<?php

namespace App\Jobs\Notification;

use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public string $deviceToken,
        public string $title,
        public string $body,
        public array  $data = []
    ) {}

    public function handle(FcmService $fcm): void
    {
        $fcm->send_to_token($this->deviceToken, ['title' => $this->title, 'body' => $this->body], array_map('strval', $this->data));
    }
}
