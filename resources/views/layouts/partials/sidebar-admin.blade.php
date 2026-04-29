@php
    $link = function ($active) {
        return $active
            ? 'flex items-center gap-3 rounded-lg bg-white/15 px-3 py-2.5 text-sm font-medium text-white'
            : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-indigo-100 hover:bg-white/10';
    };
@endphp

<div class="flex h-full min-h-0 flex-col" @click="if ($event.target.closest('a')) { sidebarOpen = false }">
    @include('layouts.partials.sidebar-brand', [
        'dashboardUrl' => route('dashboard'),
        'subtitle' => 'Panel admin',
    ])
    <nav class="min-h-0 flex-1 space-y-0.5 overflow-y-auto p-3">
        <a href="{{ route('dashboard') }}" class="{{ $link(request()->routeIs('dashboard')) }}">
            <span class="text-lg" aria-hidden="true">◆</span> Dashboard
        </a>
        <a href="{{ route('penghuni.index') }}" class="{{ $link(request()->routeIs('penghuni.*')) }}">
            <span class="text-lg" aria-hidden="true">👤</span> Data penghuni
        </a>
        <a href="{{ route('kamar.index') }}" class="{{ $link(request()->routeIs('kamar.*')) }}">
            <span class="text-lg" aria-hidden="true">🚪</span> Data kamar
        </a>
        <a href="{{ route('tagihan.index') }}" class="{{ $link(request()->routeIs('tagihan.*')) }}">
            <span class="text-lg" aria-hidden="true">📄</span> Data tagihan
        </a>
        <a href="{{ route('pembayaran.index') }}" class="{{ $link(request()->routeIs('pembayaran.*')) }}">
            <span class="text-lg" aria-hidden="true">💳</span> Verifikasi pembayaran
        </a>
        <a href="{{ route('laporan.index') }}" class="{{ $link(request()->routeIs('laporan.*')) }}">
            <span class="text-lg" aria-hidden="true">📊</span> Laporan
        </a>
        <a href="{{ route('transaksi-operasional.index') }}" class="{{ $link(request()->routeIs('transaksi-operasional.*')) }}">
            <span class="text-lg" aria-hidden="true">💰</span> Transaksi operasional
        </a>
        <a href="{{ route('kendala.index') }}" class="{{ $link(request()->routeIs('kendala.*')) }}">
            <span class="text-lg" aria-hidden="true">⚠️</span> Kendala penghuni
        </a>
    </nav>
    <div class="border-t border-white/10 p-3">
        <a href="{{ route('profile.edit') }}" class="{{ $link(request()->routeIs('profile.*')) }}">
            Profil
        </a>
    </div>
</div>
