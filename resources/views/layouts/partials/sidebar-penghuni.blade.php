@php
    $link = function ($active) {
        return $active
            ? 'flex items-center gap-3 rounded-lg bg-white/15 px-3 py-2.5 text-sm font-medium text-white'
            : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-indigo-100 hover:bg-white/10';
    };
@endphp

<div class="flex h-full min-h-0 flex-col" @click="if ($event.target.closest('a')) { sidebarOpen = false }">
    @include('layouts.partials.sidebar-brand', [
        'dashboardUrl' => route('dashboard.penghuni'),
        'subtitle' => 'Penghuni',
    ])
    <nav class="min-h-0 flex-1 space-y-0.5 overflow-y-auto p-3">
        <a href="{{ route('dashboard.penghuni') }}" class="{{ $link(request()->routeIs('dashboard.penghuni')) }}">
            Dashboard
        </a>
        <a href="{{ route('penghuni.tagihan.index') }}" class="{{ $link(request()->routeIs('penghuni.tagihan.*')) }}">
            Tagihan &amp; pembayaran
        </a>
        <a href="{{ route('penghuni.riwayat') }}" class="{{ $link(request()->routeIs('penghuni.riwayat')) }}">
            Riwayat
        </a>
        <a href="{{ route('penghuni.kendala.index') }}" class="{{ $link(request()->routeIs('penghuni.kendala.*')) }}">
            Kendala
        </a>
    </nav>
    <div class="border-t border-white/10 p-3">
        <a href="{{ route('profile.edit') }}" class="{{ $link(request()->routeIs('profile.*')) }}">
            Profil
        </a>
    </div>
</div>
