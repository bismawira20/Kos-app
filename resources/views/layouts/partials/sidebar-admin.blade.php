@php
    $link = function ($active) {
        return $active
            ? 'flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 shadow-sm'
            : 'flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900';
    };
@endphp

<div class="flex h-screen flex-col bg-white border-r border-slate-200">
    
    {{-- BRAND --}}
    <div class="shrink-0">
        @include('layouts.partials.sidebar-brand', [
            'dashboardUrl' => route('dashboard'),
            'subtitle' => 'Panel admin',
        ])
    </div>

    {{-- MENU (SCROLLABLE) --}}
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-1 scrollbar-thin scrollbar-thumb-slate-300">
        
        <a href="{{ route('dashboard') }}" class="{{ $link(request()->routeIs('dashboard')) }}">
            <span>🏠</span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('services') }}" class="{{ $link(request()->routeIs('services')) }}">
            <span>🛠️</span>
            <span>Layanan</span>
        </a>

        <a href="{{ route('akun-penghuni.index') }}" class="{{ $link(request()->routeIs('akun-penghuni.*')) }}">
            <span>👤</span>
            <span>Akun</span>
        </a>

        <a href="{{ route('penghuni.index') }}" class="{{ $link(request()->routeIs('penghuni.*')) }}">
            <span>🧍</span>
            <span>Penghuni</span>
        </a>

        <a href="{{ route('kamar.index') }}" class="{{ $link(request()->routeIs('kamar.*')) }}">
            <span>🚪</span>
            <span>Kamar</span>
        </a>

        <a href="{{ route('tagihan.index') }}" class="{{ $link(request()->routeIs('tagihan.*')) }}">
            <span>💳</span>
            <span>Tagihan</span>
        </a>

        <a href="{{ route('pembayaran.index') }}" class="{{ $link(request()->routeIs('pembayaran.*')) }}">
            <span>✅</span>
            <span>Pembayaran</span>
        </a>

        <a href="{{ route('laporan.index') }}" class="{{ $link(request()->routeIs('laporan.*')) }}">
            <span>📊</span>
            <span>Laporan</span>
        </a>

        <a href="{{ route('transaksi-operasional.index') }}" class="{{ $link(request()->routeIs('transaksi-operasional.*')) }}">
            <span>📦</span>
            <span>Operasional</span>
        </a>

        <a href="{{ route('kendala.index') }}" class="{{ $link(request()->routeIs('kendala.*')) }}">
            <span>⚠️</span>
            <span>Kendala</span>
        </a>

    </div>

    {{-- PROFILE --}}
    <div class="shrink-0 border-t border-slate-200 p-3 bg-white">
        <a href="{{ route('profile.edit') }}" class="{{ $link(request()->routeIs('profile.*')) }}">
            <span>⚙️</span>
            <span>Profil</span>
        </a>
    </div>
</div>