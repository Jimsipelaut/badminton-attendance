<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal Backup Basis Data Otomatis Harian Pukul 02:00 WIB
Schedule::command('db:backup --prune=7')->dailyAt('02:00');
