<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('points:expire')->daily();
Schedule::command('discount:update-status')->daily();

Schedule::command('customers:update-grade')->monthlyOn(1, '00:00');