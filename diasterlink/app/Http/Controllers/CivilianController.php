<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CivilianController extends Controller
{
    public function dashboard()
    {
        return view('civilian.dashboard');
    }

    public function profile()
    {
        return view('civilian.profile');
    }
}
