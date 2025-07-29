<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'responder':
                return redirect()->route('responder.dashboard');
            case 'civilian':
                return redirect()->route('civilian.dashboard');
            default:
                return redirect()->route('civilian.dashboard');
        }
    }
}
