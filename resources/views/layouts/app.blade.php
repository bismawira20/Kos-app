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
        {{-- Mobile: overlay --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/50 md:hidden"
            @click="sidebarOpen = false"
        ></div>

        <div class="flex min-h-screen">
            {{-- Sidebar kiri: off-canvas di mobile, tetap di samping di desktop --}}
            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-64 max-w-[85vw] shrink-0 -translate-x-full flex-col border-r border-indigo-700/50 bg-gradient-to-b from-indigo-800 to-violet-950 text-white shadow-xl transition-transform duration-200 ease-out md:static md:max-w-none md:translate-x-0 md:shadow-none"
                :class="{ '!translate-x-0': sidebarOpen }"
            >
                <div class="flex items-center justify-end border-b border-white/10 px-2 py-2 md:hidden">
                    <button type="button" class="rounded-lg p-2 text-indigo-100 hover:bg-white/10" @click="sidebarOpen = false" aria-label="Tutup menu">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @if(Auth::user()->role === 'admin')
                    @include('layouts.partials.sidebar-admin')
                @else
                    @include('layouts.partials.sidebar-penghuni')
                @endif
            </aside>

            <div class="flex min-w-0 flex-1 flex-col md:pl-0">
                <header class="sticky top-0 z-30 flex items-center justify-between gap-3 border-b border-slate-200 bg-white px-3 py-3 shadow-sm sm:px-4">
                    <div class="flex min-w-0 items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex rounded-lg border border-slate-200 p-2 text-slate-700 hover:bg-slate-50 md:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Buka menu"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div class="hidden items-center gap-2 sm:flex">
                            <img src="{{ asset('images/epaykos-logo.svg') }}" alt="" class="h-8 w-auto opacity-90" width="128" height="32">
                            <span class="truncate text-sm font-semibold text-slate-600">{{ config('app.name', 'E-PayKos') }}</span>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                        <span class="hidden max-w-[10rem] truncate text-sm text-slate-600 sm:inline md:max-w-xs">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 sm:text-sm">Keluar</button>
                        </form>
                    </div>
                </header>

                @isset($header)
                    <div class="border-b border-slate-200 bg-white px-4 py-5 sm:px-8">
                        {{ $header }}
                    </div>
                @endisset

                <main class="flex-1 p-4 sm:p-8">
                    @if (session('status'))
                        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-900 ring-1 ring-emerald-200">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-900 ring-1 ring-red-200">
                            {{ session('error') }}
                        </div>
                    @endif
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
