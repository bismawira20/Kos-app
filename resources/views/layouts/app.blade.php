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
        @stack('scripts')
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900" x-data="{ sidebarOpen: false }">
        <!-- Sidebar overlay untuk mobile -->
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-950/50 md:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- NAVBAR FIXED -->
        <header class="fixed top-0 left-0 right-0 z-50 border-b-2 border-red-900/30 bg-gradient-to-r from-red-700 to-red-800 text-white shadow-2xl shadow-red-950/30 h-16 backdrop-blur-sm">
            <div class="flex h-full w-full items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-4 flex-1">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg border border-white/20 bg-white/10 p-2.5 text-white transition hover:bg-white/20 md:hidden"
                        @click="sidebarOpen = true"
                        aria-label="Buka menu"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ Auth::user()->role === 'admin' ? route('dashboard') : route('dashboard.penghuni') }}" class="flex min-w-0 items-center gap-3 hover:opacity-90 transition">
                        <img src="{{ asset('images/epaykos-logo.svg') }}" alt="" class="h-9 w-auto rounded-lg bg-white/15 p-1.5" width="128" height="32">
                        <div class="min-w-0 hidden sm:block">
                            <span class="block truncate text-sm font-bold leading-5 text-white">{{ config('app.name', 'E-PayKos') }}</span>
                            <span class="block truncate text-xs text-red-100">Panel manajemen</span>
                        </div>
                    </a>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="hidden md:flex items-center gap-2 rounded-lg bg-white/10 px-3 py-2 backdrop-blur-sm border border-white/10">
                        <span class="text-sm font-medium text-white truncate max-w-xs">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-red-100 hidden lg:inline">• {{ ucfirst(Auth::user()->role) }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-red-700 shadow-lg shadow-red-900/20 transition hover:bg-red-50 hover:shadow-xl active:scale-95">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main content area dengan padding top untuk navbar fixed -->
        <div class="flex pt-16 min-h-screen w-full">
            <!-- Sidebar -->
            <aside
                class="fixed inset-y-16 left-0 z-40 w-72 -translate-x-full border-r border-slate-200 bg-white shadow-xl shadow-slate-950/10 transition-transform duration-200 ease-out md:sticky md:top-16 md:w-72 md:translate-x-0 md:shadow-none h-[calc(100vh-64px)]"
                :class="{ '!translate-x-0': sidebarOpen }"
            >
                <div class="flex h-full min-h-0 flex-col overflow-hidden">
                    <div class="flex items-center justify-end border-b border-slate-200 px-3 py-3 md:hidden">
                        <button type="button" class="rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-50" @click="sidebarOpen = false" aria-label="Tutup menu">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @if(Auth::user()->role === 'admin')
                        @include('layouts.partials.sidebar-admin')
                    @else
                        @include('layouts.partials.sidebar-penghuni')
                    @endif
                </div>
            </aside>

            <!-- Main content -->
            <div class="flex-1 flex flex-col min-w-0 w-full md:ml-0">
                @isset($header)
                    <div class="border-b border-slate-200 bg-white px-4 py-5 sm:px-8">
                        {{ $header }}
                    </div>
                @endisset

                <main class="flex-1 bg-slate-100 p-4 sm:p-8 overflow-x-hidden">
                    @if (session('status'))
                        <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-900 ring-1 ring-emerald-200">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-900 ring-1 ring-rose-200">
                            {{ session('error') }}
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
