<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscriptions:check-expired')->daily()->at('00:05');
Schedule::command('modules:clean-expired')->daily()->at('01:00');
Schedule::command('notifications:generate')->daily()->at('06:00');
Schedule::command('notifications:cleanup')->weekly()->mondays()->at('02:00');

Schedule::command('audit:cleanup')->weeklyOn(0, '03:00'); // Sundays at 3 AM
