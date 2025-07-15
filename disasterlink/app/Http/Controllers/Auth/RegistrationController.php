<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        // Step 1: Validate form fields and reCAPTCHA presence
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => 'required', // make sure the checkbox was clicked
        ]);

        // Step 2: Verify reCAPTCHA token with Google
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
        ]);

        if (!($response->json('success') === true)) {
            return back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.'])->withInput();
        }

        // Step 3: Create the user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_CIVILIAN,
            'is_active' => true,
        ]);

        // Step 4: Redirect to login with success message
        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }
}
