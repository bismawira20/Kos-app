
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
        <aside class="w-72 bg-white border-r border-slate-200 flex flex-col shrink-0 h-full">
            
            {{-- BRAND --}}
            @if(Auth::user()->role === 'admin')
                @include('layouts.partials.sidebar-brand', [
                    'dashboardUrl' => route('dashboard'),
                    'subtitle' => 'Panel admin',
                ])
            @else
                @include('layouts.partials.sidebar-brand', [
                    'dashboardUrl' => route('dashboard.penghuni'),
                    'subtitle' => 'Penghuni',
                ])
            @endif

            <!-- MENU SCROLL -->
            <div class="flex-1 overflow-y-auto p-4 space-y-1">
                @if(Auth::user()->role === 'admin')
                    @include('layouts.partials.sidebar-admin')
                @else
                    @include('layouts.partials.sidebar-penghuni')
                @endif
            </div>

            <!-- PROFILE / FOOTER -->
            <div class="border-t border-slate-200 p-4 flex justify-between items-center bg-white shrink-0">
                <a href="{{ route('profile.edit') }}" class="text-sm text-slate-600 hover:text-slate-900 flex items-center gap-2">
                    <span>⚙️</span>
                    <span>Profil</span>
                </a>
                <button type="button" onclick="document.getElementById('logout-modal').showModal()" class="text-sm text-red-600 hover:text-red-900 font-medium">
                    Logout
                </button>
            </div>

        </aside>

        <!-- CONTENT -->
        <div class="flex-1 flex flex-col min-w-0">

            <main class="flex-1 overflow-y-auto p-6">
                
                @isset($header)
                    <div class="bg-white border border-slate-200 rounded-2xl px-6 py-5 shadow-sm mb-6 min-h-[96px] flex items-center shrink-0">
                        <div class="w-full">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                @if (session('status'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="mb-6 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="font-medium text-emerald-900">{{ session('status') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 focus:outline-none transition active:scale-90">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="mb-6 flex items-center justify-between rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="font-medium text-rose-900">{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-800 focus:outline-none transition active:scale-90">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @yield('content')
                {{ $slot ?? '' }}

            </main>

        </div>

    </div>

    @stack('scripts')

    <!-- LOGOUT CONFIRMATION MODAL -->
    <dialog id="logout-modal" class="w-full max-w-sm rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden">
        <div class="p-6 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 text-rose-600 mb-4">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Konfirmasi Logout</h3>
            <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin logout?</p>
            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="document.getElementById('logout-modal').close()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition active:scale-95">
                    Batal
                </button>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 shadow-sm transition active:scale-95">
                        Ya, Lanjutkan
                    </button>
                </form>
            </div>
        </div>
    </dialog>
</body>
</html>
