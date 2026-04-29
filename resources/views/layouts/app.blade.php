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
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-950/50 md:hidden"
            @click="sidebarOpen = false"
        ></div>

        <div class="flex min-h-screen flex-col">
            <header class="sticky top-0 z-50 border-b border-red-800/80 bg-gradient-to-r from-red-700 to-red-800 text-white shadow-lg shadow-red-950/20">
                <div class="mx-auto flex h-16 max-w-[100vw] items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/10 p-2 text-white transition hover:bg-white/15 md:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Buka menu"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <a href="{{ Auth::user()->role === 'admin' ? route('dashboard') : route('dashboard.penghuni') }}" class="flex min-w-0 items-center gap-3">
                            <img src="{{ asset('images/epaykos-logo.svg') }}" alt="" class="h-9 w-auto rounded-xl bg-white/10 p-1" width="128" height="32">
                            <div class="min-w-0">
                                <span class="block truncate text-sm font-semibold leading-5 text-white">{{ config('app.name', 'E-PayKos') }}</span>
                                <span class="block truncate text-xs text-red-100">Panel pengelolaan kos</span>
                            </div>
                        </a>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="hidden max-w-[14rem] rounded-full bg-white/10 px-3 py-1.5 text-sm font-medium text-white sm:block">
                            <span class="truncate">{{ Auth::user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-white px-3.5 py-2 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50">Keluar</button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="flex min-h-0 flex-1">
                <aside
                    class="fixed inset-y-16 left-0 z-40 w-72 -translate-x-full border-r border-slate-200 bg-white shadow-xl shadow-slate-950/10 transition-transform duration-200 ease-out md:sticky md:top-16 md:z-0 md:h-[calc(100vh-4rem)] md:translate-x-0 md:shadow-none"
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

                <div class="min-w-0 flex-1 md:ml-72">
                    @isset($header)
                        <div class="border-b border-slate-200 bg-white px-4 py-5 sm:px-8">
                            {{ $header }}
                        </div>
                    @endisset

                    <main class="min-h-[calc(100vh-4rem)] bg-slate-100 p-4 sm:p-8">
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
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
