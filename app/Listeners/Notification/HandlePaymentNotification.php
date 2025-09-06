<?php

namespace App\Listeners\Notification;

use App\Events\Notification\PaymentCompleted;
use App\Events\Notification\PaymentFailed;
use App\Jobs\Notification\SendPushNotificationJob;
use App\Models\Notification;

class HandlePaymentNotification
{
    public function handle($event): void
    {
        if ($event instanceof PaymentCompleted) {
            $title = 'تم الدفع بنجاح';
            $message = "تم دفع دورة {$event->course->course_name} بمبلغ {$event->amount}€ عبر {$event->method}.";
            $type = 'payment_success';
            $data = [
                'status' => 'success',
                'method' => $event->method,
                'course_id' => $event->course->id,
                'amount' => $event->amount,
            ];
            $user = $event->user;
        } elseif ($event instanceof PaymentFailed) {
            $title = 'فشل الدفع';
            $message = "فشل الدفع".($event->course ? " لدورة {$event->course->course_name}" : '').($event->reason ? " — السبب: {$event->reason}" : '');
            $type = 'payment_failed';
            $data = [
                'status' => 'failed',
                'method' => $event->method,
                'course_id' => $event->course?->id,
                'amount' => $event->amount,
                'reason' => $event->reason,
            ];
            $user = $event->user;
        } else {
            return;
        }

        if ($user) {
            Notification::create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'is_read' => false,
                'user_id' => $user->id,
            ]);

            $deviceToken = $user->fcm_token ?? '';
            if ($deviceToken) {
//                SendPushNotificationJob::dispatch($deviceToken, $title, $message, $data)
//                    ->onQueue('notifications');
                dispatch(new SendPushNotificationJob($deviceToken,$title,$message,$data));
            }
        }
    }
}
