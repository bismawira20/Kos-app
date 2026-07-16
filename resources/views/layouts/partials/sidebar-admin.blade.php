@php
    $link = function ($active) {
        return $active
            ? 'flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 shadow-sm'
            : 'flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900';
    };
@endphp

<a href="{{ route('dashboard') }}" class="{{ $link(request()->routeIs('dashboard')) }}">
    <span>🏠</span>
    <span>Dashboard</span>
</a>


<a href="{{ route('akun-penghuni.index') }}" class="{{ $link(request()->routeIs('akun-penghuni.*')) }}">
    <span>👤</span>
    <span>Kelola Akun</span>
</a>

<a href="{{ route('penghuni.index') }}" class="{{ $link(request()->routeIs('penghuni.*')) }}">
    <span>🧍</span>
    <span>Kelola Penghuni</span>
</a>

<a href="{{ route('kamar.index') }}" class="{{ $link(request()->routeIs('kamar.*')) }}">
    <span>🚪</span>
    <span>Kelola Kamar</span>
</a>

<a href="{{ route('tipe-kamar.index') }}" class="{{ $link(request()->routeIs('tipe-kamar.*')) }}">
    <span>🏷️</span>
    <span>Harga Kamar</span>
</a>

<a href="{{ route('tagihan.index') }}" class="{{ $link(request()->routeIs('tagihan.*')) }}">
    <span>💳</span>
    <span>Kelola Tagihan</span>
</a>

<a href="{{ route('pembayaran.index') }}" class="{{ $link(request()->routeIs('pembayaran.*')) }}">
    <span>✅</span>
    <span>Kelola Pembayaran</span>
</a>

<a href="{{ route('laporan.index') }}" class="{{ $link(request()->routeIs('laporan.*')) }}">
    <span>📊</span>
    <span>Laporan Keuangan</span>
</a>


<a href="{{ route('kendala.index') }}" class="{{ $link(request()->routeIs('kendala.*')) }}">
    <span>⚠️</span>
    <span>Laporan Kendala</span>
</a>