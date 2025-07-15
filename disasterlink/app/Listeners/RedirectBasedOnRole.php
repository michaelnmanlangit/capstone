<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class RedirectBasedOnRole
{
    public function handle(Login $event): void
    {
        $role = $event->user->role;

        $redirect = match ($role) {
            'admin' => route('admin.dashboard'),
            'responder' => route('responder.home'),
            'civilian' => route('civilian.home'),
            default => '/',
        };

        Session::put('url.intended', $redirect);
    }
}
