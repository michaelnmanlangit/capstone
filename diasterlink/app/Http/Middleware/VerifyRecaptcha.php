<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip reCAPTCHA verification in testing environment
        if (app()->environment('testing')) {
            return $next($request);
        }

        // Skip if no reCAPTCHA keys are configured
        if (empty(config('services.recaptcha.secret_key'))) {
            return $next($request);
        }

        $recaptchaResponse = $request->input('g-recaptcha-response');

        if (empty($recaptchaResponse)) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Please complete the reCAPTCHA verification.'
            ])->withInput();
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $recaptchaResponse,
            'remoteip' => $request->ip(),
        ]);

        $responseData = $response->json();

        if (!$responseData['success']) {
            return back()->withErrors([
                'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.'
            ])->withInput();
        }

        return $next($request);
    }
}
