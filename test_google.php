<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $gEvent = new Spatie\GoogleCalendar\Event;
    $gEvent->name = 'Test euyyyy pak';
    $gEvent->startDate = Carbon\Carbon::parse('2026-07-06');
    $gEvent->endDate = Carbon\Carbon::parse('2026-07-06')->addDay();
    $gEvent = $gEvent->save();
    echo 'Oke gas nomor dua gas wkwk: ' . $gEvent->id;
} catch (\Exception $ex) {
    echo 'ERROR: ' . $ex->getMessage();
}
