<?php

namespace App\Providers;

<<<<<<< HEAD
use App\Listeners\SendResetCodeEmail;
use App\Listeners\SendVerificationEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
=======
>>>>>>> ca7ced0 (first version: database, models and spatie role)
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
    public function boot(): void
    {
<<<<<<< HEAD
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
//        Gate::before(function($user, $ability){
//            return $user->hasRole('admin') ? true : null;
//        });

        Event::listen(SendVerificationEmail::class);
        Event::listen(SendResetCodeEmail::class);
=======
        //
>>>>>>> ca7ced0 (first version: database, models and spatie role)
    }
}
