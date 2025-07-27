<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\SendEmailVerificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendVerificationEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {



//        dispatch(new SendEmailVerificationJob($event->user));

        SendEmailVerificationJob::dispatch($event->user)->delay(now()->addMinutes(1));

    }
}
