<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $routeMiddleware = [
        'custom.auth' => \App\Http\Middleware\AuthMiddleware::class,
    ];
}
