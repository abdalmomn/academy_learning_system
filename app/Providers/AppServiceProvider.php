<?php

namespace App\Providers;

use App\Events\Notification\PaymentCompleted;
use App\Events\Notification\PaymentFailed;
use App\Listeners\Notification\HandlePaymentNotification;
use App\Listeners\SendPinCodeEmail;
use App\Listeners\SendResetCodeEmail;
use App\Listeners\SendVerificationEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    protected $listen = [
        PaymentCompleted::class => [
            HandlePaymentNotification::class,
        ],
        PaymentFailed::class => [
            HandlePaymentNotification::class,
        ],
    ];
    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
//        Gate::before(function($user, $ability){
//            return $user->hasRole('admin') ? true : null;
//        });

        Event::listen(SendVerificationEmail::class);
        Event::listen(SendResetCodeEmail::class);
        Event::listen(SendPinCodeEmail::class);

    }
}
