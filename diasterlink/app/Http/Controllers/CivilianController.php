<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CivilianController extends Controller
{
    public function dashboard()
    {
        return view('civilian.dashboard');
    }

    public function reportIncident()
    {
        return view('civilian.report-incident');
    }

    public function sendSOS()
    {
        return view('civilian.send-sos');
    }

    public function profile()
    {
        return view('civilian.profile');
    }
}
