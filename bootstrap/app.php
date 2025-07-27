<?php

<<<<<<< HEAD
use App\Http\Middleware\CheckIfUserBanned;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Schedule;
=======
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
>>>>>>> ca7ced0 (first version: database, models and spatie role)

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
<<<<<<< HEAD
        $middleware->alias([
            'isBanned' => CheckIfUserBanned::class,
        ]);
=======
        //
>>>>>>> ca7ced0 (first version: database, models and spatie role)
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
