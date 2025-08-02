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

    public function sos()
    {
        return view('civilian.sos');
    }

    public function incident()
    {
        return view('civilian.incident');
    }
}
