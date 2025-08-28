<?php

namespace App\Listeners;

use App\Events\FamilyPinCode;
use App\Jobs\SendPinCodeEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPinCodeEmail
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
    public function handle(FamilyPinCode $event): void
    {
        dispatch(new SendPinCodeEmailJob($event->user,$event->code));
    }
}
