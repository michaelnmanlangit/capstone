<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponderController extends Controller
{
    public function dashboard()
    {
        return view('responder.dashboard');
    }

    public function incidents()
    {
        // TODO: Implement incident management
        return view('responder.incidents');
    }

    public function profile()
    {
        return view('responder.profile');
    }
}
