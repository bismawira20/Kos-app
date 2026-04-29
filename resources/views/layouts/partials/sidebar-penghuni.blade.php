@php
    $link = function ($active) {
        return $active
            ? 'flex items-center gap-3 rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 ring-1 ring-red-100'
            : 'flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50';
    };
@endphp

<div class="flex h-full min-h-0 flex-col bg-white" @click="if ($event.target.closest('a')) { sidebarOpen = false }">
    @include('layouts.partials.sidebar-brand', [
        'dashboardUrl' => route('dashboard.penghuni'),
        'subtitle' => 'Penghuni',
    ])
    <div class="flex-1 overflow-y-auto px-3 py-4">
        <nav class="space-y-2">
            <a href="{{ route('services') }}" class="{{ $link(request()->routeIs('services')) }}">
                <span class="text-lg" aria-hidden="true">🎯</span> Layanan
            </a>
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
    </div>
    <div class="border-t border-slate-200 p-3">
        <a href="{{ route('profile.edit') }}" class="{{ $link(request()->routeIs('profile.*')) }}">
            <span class="text-lg" aria-hidden="true">⚙️</span> Profil
        </a>
    </div>
</div>
