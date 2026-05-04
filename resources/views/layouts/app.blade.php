
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E-PayKos') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-900">

    <!-- NAVBAR -->
    @include('layouts.navigation')
    <!-- MAIN LAYOUT -->
    <div class="flex pt-16 h-screen overflow-hidden">

        <!-- SIDEBAR (NO OVERLAY, FULL HEIGHT) -->
        <aside class="w-72 bg-white border-r border-slate-200 flex flex-col">

            <!-- MENU SCROLL -->
            <div class="flex-1 overflow-y-auto p-4 space-y-1">

                @if(Auth::user()->role === 'admin')
                    @include('layouts.partials.sidebar-admin')
                @else
                    @include('layouts.partials.sidebar-penghuni')
                @endif

            </div>

            <!-- PROFILE / FOOTER -->
            <div class="border-t border-slate-200 p-4 flex justify-between items-center">
                <a href="{{ route('profile.edit') }}" class="text-sm text-slate-600 hover:text-slate-900">
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-900 font-medium">
                        Logout
                    </button>
                </form>
            </div>

        </aside>

        <!-- CONTENT -->
        <div class="flex-1 flex flex-col min-w-0">

            @isset($header)
                <div class="bg-white border-b border-slate-200 px-6 py-4">
                    {{ $header }}
                </div>
            @endisset

            <main class="flex-1 overflow-y-auto p-6">
                
                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-green-50 p-3 text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-lg bg-red-50 p-3 text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
                {{ $slot ?? '' }}

            </main>

        </div>

    </div>

    @stack('scripts')
</body>
</html>
