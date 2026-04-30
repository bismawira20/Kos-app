@php
    $link = function ($active) {
        return $active
            ? 'flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-semibold text-red-700 bg-red-50 ring-1 ring-red-100 truncate'
            : 'flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 truncate';
    };
@endphp

<div class="flex h-full min-h-0 flex-col bg-white" @click="if ($event.target.closest('a')) { sidebarOpen = false }">
    @include('layouts.partials.sidebar-brand', [
        'dashboardUrl' => route('dashboard.penghuni'),
        'subtitle' => 'Penghuni',
    ])
    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <div class="space-y-1">
            <a href="{{ route('services') }}" class="{{ $link(request()->routeIs('services')) }}">
                <span class="text-lg flex-shrink-0" aria-hidden="true">??</span>
                <span class="truncate">Layanan</span>
            </a>
            <a href="{{ route('dashboard.penghuni') }}" class="{{ $link(request()->routeIs('dashboard.penghuni')) }}">
                <span class="text-lg flex-shrink-0" aria-hidden="true">??</span>
                <span class="truncate">Dashboard</span>
            </a>
            <a href="{{ route('penghuni.tagihan.index') }}" class="{{ $link(request()->routeIs('penghuni.tagihan.*')) }}">
                <span class="text-lg flex-shrink-0" aria-hidden="true">??</span>
                <span class="truncate">Tagihan &amp; pembayaran</span>
            </a>
            <a href="{{ route('penghuni.riwayat') }}" class="{{ $link(request()->routeIs('penghuni.riwayat')) }}">
                <span class="text-lg flex-shrink-0" aria-hidden="true">??</span>
                <span class="truncate">Riwayat</span>
            </a>
            <a href="{{ route('penghuni.kendala.index') }}" class="{{ $link(request()->routeIs('penghuni.kendala.*')) }}">
                <span class="text-lg flex-shrink-0" aria-hidden="true">??</span>
                <span class="truncate">Kendala</span>
            </a>
        </div>
    </nav>
    <div class="shrink-0 border-t border-slate-200 p-3">
        <a href="{{ route('profile.edit') }}" class="{{ $link(request()->routeIs('profile.*')) }}">
            <span class="text-lg flex-shrink-0" aria-hidden="true">??</span>
            <span class="truncate">Profil</span>
        </a>
    </div>
</div>
