<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Login;
use App\Listeners\RedirectBasedOnRole;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            RedirectBasedOnRole::class,
        ],
    ];
}
