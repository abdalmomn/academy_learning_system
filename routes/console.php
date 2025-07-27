<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
<<<<<<< HEAD
use Illuminate\Support\Facades\Schedule;
=======
>>>>>>> ca7ced0 (first version: database, models and spatie role)

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
<<<<<<< HEAD
Schedule::command('reset_password:cleanup')->everyTenMinutes();
Schedule::command('users:unban-expired')->hourly();
=======
>>>>>>> ca7ced0 (first version: database, models and spatie role)
