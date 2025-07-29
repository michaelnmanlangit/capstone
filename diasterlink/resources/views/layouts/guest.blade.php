<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DISASTERLINK') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- reCAPTCHA Responsive Styles -->
        <style>
            .g-recaptcha {
                margin: 0 auto;
                display: block;
                width: 100% !important;
                max-width: 304px;
                height: 78px;
                background: #f9f9f9;
                border: 1px solid #d3d3d3;
                border-radius: 3px;
                overflow: hidden;
            }
            
            .g-recaptcha > div {
                width: 100% !important;
                height: 100% !important;
            }
            
            .g-recaptcha iframe {
                width: 100% !important;
                height: 100% !important;
            }
            
            @media screen and (max-width: 480px) {
                .g-recaptcha {
                    transform: scale(0.95);
                    -webkit-transform: scale(0.95);
                    transform-origin: center center;
                    -webkit-transform-origin: center center;
                }
            }
            
            @media screen and (max-width: 360px) {
                .g-recaptcha {
                    transform: scale(0.85);
                    -webkit-transform: scale(0.85);
                }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-sm sm:max-w-md space-y-8 bg-white p-6 sm:p-8 rounded-lg shadow-lg">
                <div class="text-center">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">DISASTERLINK</h2>
                </div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
