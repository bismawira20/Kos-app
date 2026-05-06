@php
    $link = function ($active) {
        return $active
            ? 'flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-red-700 bg-red-50 ring-1 ring-red-100 truncate'
            : 'flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 truncate';
    };
@endphp

<div class="space-y-1">
    <a href="{{ route('services') }}" class="{{ $link(request()->routeIs('services')) }}">
        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
        <span class="truncate">Layanan</span>
    </a>
    <a href="{{ route('dashboard.penghuni') }}" class="{{ $link(request()->routeIs('dashboard.penghuni')) }}">
        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
        <span class="truncate">Dashboard</span>
    </a>
    <a href="{{ route('penghuni.tagihan.index') }}" class="{{ $link(request()->routeIs('penghuni.tagihan.*')) }}">
        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        <span class="truncate">Tagihan &amp; pembayaran</span>
    </a>
    <a href="{{ route('penghuni.riwayat') }}" class="{{ $link(request()->routeIs('penghuni.riwayat')) }}">
        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span class="truncate">Riwayat</span>
    </a>
    <a href="{{ route('penghuni.kendala.index') }}" class="{{ $link(request()->routeIs('penghuni.kendala.*')) }}">
        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        <span class="truncate">Kendala</span>
    </a>
</div>
