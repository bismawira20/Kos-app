@php
    $link = function ($active) {
        return $active
            ? 'flex items-center gap-3 rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 ring-1 ring-red-100'
            : 'flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50';
    };
@endphp

<div class="flex h-full min-h-0 flex-col bg-white" @click="if ($event.target.closest('a')) { sidebarOpen = false }">
    @include('layouts.partials.sidebar-brand', [
        'dashboardUrl' => route('dashboard'),
        'subtitle' => 'Panel admin',
    ])
    <div class="flex-1 overflow-y-auto px-3 py-4">
        <nav class="space-y-2">
            <a href="{{ route('services') }}" class="{{ $link(request()->routeIs('services')) }}">
                <span class="text-lg" aria-hidden="true">🎯</span> Layanan
            </a>
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
    </div>
    <div class="border-t border-slate-200 p-3">
        <a href="{{ route('profile.edit') }}" class="{{ $link(request()->routeIs('profile.*')) }}">
            <span class="text-lg" aria-hidden="true">⚙️</span> Profil
        </a>
    </div>
</div>
