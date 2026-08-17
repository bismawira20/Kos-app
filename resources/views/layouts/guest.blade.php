<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-Kos') }} — Masuk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-600 via-violet-600 to-indigo-900 antialiased">
    <div class="flex min-h-screen items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl shadow-indigo-900/40 flex flex-col md:flex-row min-h-[520px]">
            <div class="md:w-1/2 bg-gradient-to-br from-indigo-800 to-violet-900 p-8 sm:p-10 text-white flex flex-col justify-center">
                <div class="mb-6 flex justify-center">
                    <div class="flex h-28 w-28 items-center justify-center rounded-full bg-white/10 ring-2 ring-white/30">
                        <svg class="h-14 w-14 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-center text-2xl font-bold tracking-tight">Selamat Datang</h1>
                <p class="mt-3 text-center text-sm leading-relaxed text-indigo-100">
                    Mudah kelola pembayaran kos bulanan dengan cepat &amp; transparan — {{ config('app.name', 'E-Kos') }}.
                </p>
            </div>
            <div class="md:w-1/2 flex flex-col justify-center p-8 sm:p-10 bg-white">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
