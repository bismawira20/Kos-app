@php
    $isAdmin = Auth::user()->role === 'admin';
    $home = $isAdmin ? route('dashboard') : route('dashboard.penghuni');

    // Dynamically identify the page title based on the active route
    $pageTitle = 'E-PayKos';
    if (request()->routeIs('dashboard') || request()->routeIs('dashboard.penghuni')) {
        $pageTitle = 'Dashboard';
    } elseif (request()->routeIs('services')) {
        $pageTitle = 'Layanan';
    } elseif (request()->routeIs('akun-penghuni.*')) {
        $pageTitle = 'Akun Penghuni';
    } elseif (request()->routeIs('penghuni.*')) {
        $pageTitle = 'Data Penghuni';
    } elseif (request()->routeIs('kamar.*')) {
        $pageTitle = 'Data Kamar';
    } elseif (request()->routeIs('tagihan.*')) {
        $pageTitle = 'Tagihan';
    } elseif (request()->routeIs('pembayaran.*')) {
        $pageTitle = 'Pembayaran';
    } elseif (request()->routeIs('laporan.*')) {
        $pageTitle = 'Laporan Keuangan';
    } elseif (request()->routeIs('transaksi-operasional.*')) {
        $pageTitle = 'Biaya Operasional';
    } elseif (request()->routeIs('kendala.*') || request()->routeIs('penghuni.kendala.*')) {
        $pageTitle = 'Laporan Kendala';
    } elseif (request()->routeIs('penghuni.tagihan.*')) {
        $pageTitle = 'Tagihan & Pembayaran';
    } elseif (request()->routeIs('penghuni.riwayat')) {
        $pageTitle = 'Riwayat';
    } elseif (request()->routeIs('profile.*')) {
        $pageTitle = 'Profil Saya';
    }
@endphp

<nav x-data="{ open: false }" class="fixed top-0 left-0 w-full z-50 border-b border-slate-200 bg-gradient-to-r from-slate-950 via-indigo-950 to-violet-950 text-white shadow-lg shadow-slate-950/15">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 flex-wrap items-center justify-between gap-3 py-3">
            <div class="flex min-w-0 items-center gap-3">
                <a href="{{ $home }}" class="flex min-w-0 items-center gap-3 rounded-2xl bg-white/10 px-3 py-2 font-semibold text-white backdrop-blur transition hover:bg-white/15">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white shadow-sm">
                        <img src="{{ asset('images/epaykos-logo.png') }}" alt="ePayKos" class="h-full w-full object-cover">
                    </div>
                    <span class="text-lg font-bold tracking-tight">ePayKos</span>
                </a>
            </div>

            <div class="hidden sm:flex min-w-0 flex-1 items-center justify-center">
                <span class="text-base font-semibold tracking-wider text-indigo-100 uppercase bg-white/10 px-4 py-1.5 rounded-xl border border-white/5 shadow-inner">
                    {{ $pageTitle }}
                </span>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-white/10 px-3 py-2 text-sm font-medium text-white transition hover:bg-white/15 focus:outline-none">
                            <div class="max-w-[10rem] truncate">{{ Auth::user()->name }}</div>
                            <div>
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button type="button" @click="open = ! open" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/10 p-2 text-white transition hover:bg-white/15 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-white/10 bg-slate-950/95 backdrop-blur-sm sm:hidden">
        <div class="space-y-1 px-3 py-3">
            @if ($isAdmin)
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kamar.index')" :active="request()->routeIs('kamar.*')">
                    Kamar
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('penghuni.index')" :active="request()->routeIs('penghuni.*')">
                    Penghuni
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pembayaran.index')" :active="request()->routeIs('pembayaran.*')">
                    Pembayaran
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('dashboard.penghuni')" :active="request()->routeIs('dashboard.penghuni')">
                    Dashboard
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-white/10 px-3 pb-3 pt-4 text-white">
            <div class="rounded-2xl bg-white/10 px-4 py-3 backdrop-blur-sm">
                <div class="font-semibold text-white">{{ Auth::user()->name }}</div>
                <div class="mt-0.5 text-sm text-indigo-100">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
{{-- spacer to avoid page content being hidden under fixed navbar --}}
