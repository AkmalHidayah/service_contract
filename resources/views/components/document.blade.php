<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
<body class="font-sans antialiased bg-gray-100 text-sm sm:text-base font-inter">


    <!-- Scripts -->
        <link rel="stylesheet" href="{{ asset('build/assets/app-BJr1-sId.css') }}">
</head>
<body class="font-sans antialiased bg-gray-100 text-sm sm:text-base">

<div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center p-4">

<div class="bg-white shadow-lg sm:rounded-lg max-w-7xl max-w-full w-full p-6">

            {{ $slot }}
        </div>
    </div>
    <script src="{{ asset('build/assets/app-CH09qwMe.js') }}" defer></script>
</body>
</html>
