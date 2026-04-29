<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-violet-500">Penghuni dashboard</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                    Selamat datang{{ $penghuni ? ', '.$penghuni->nama : '' }}
                </h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">Pantau tagihan, riwayat pembayaran, dan laporan kendala dari satu halaman yang ringkas.</p>
            </div>
            @if ($penghuni)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('penghuni.tagihan.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Bayar tagihan</a>
                    <a href="{{ route('penghuni.kendala.create') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Laporkan kendala</a>
                </div>
            @endif
        </div>
    </x-slot>

    @if (! $penghuni)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-950">
            <h3 class="font-semibold">Akun belum terhubung</h3>
            <p class="mt-2 text-sm leading-relaxed">Hubungi admin agar data penghuni dihubungkan ke akun Anda.</p>
        </div>
    @else
        @php
            $tb = $stats['tagihan_bulan_ini'] ?? null;
            $hari = $stats['hari_jatuh_tempo'] ?? null;
        @endphp
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-3xl bg-gradient-to-br from-indigo-700 to-violet-900 p-5 text-white shadow-lg shadow-indigo-950/10">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">Kamar saya</p>
                <p class="mt-4 text-4xl font-semibold tracking-tight">{{ $penghuni->kamar?->nomor_kamar ?? '—' }}</p>
                <p class="mt-2 text-sm text-indigo-100">Lantai {{ $penghuni->kamar?->lantai ?? '—' }}</p>
            </article>

            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tagihan bulan ini</p>
                <p class="mt-4 text-3xl font-semibold tracking-tight text-slate-900">{{ $tb ? 'Rp '.number_format($tb->jumlah, 0, ',', '.') : '—' }}</p>
                @if ($tb)
                    <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $tb->status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : ($tb->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                        {{ $tb->status === 'lunas' ? 'Lunas' : ($tb->status === 'menunggu' ? 'Menunggu verifikasi' : 'Belum bayar') }}
                    </span>
                @endif
            </article>

            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tagihan tertunggak</p>
                <p class="mt-4 text-4xl font-semibold tracking-tight text-rose-600">{{ $stats['tunggakan'] ?? 0 }}</p>
                <p class="mt-2 text-sm text-slate-500">Jatuh tempo lewat</p>
            </article>

            <article class="rounded-3xl bg-slate-950 p-5 text-white shadow-lg shadow-slate-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Jatuh tempo</p>
                @if ($tb && $tb->jatuh_tempo)
                    <p class="mt-4 text-2xl font-semibold tracking-tight">{{ $tb->jatuh_tempo->translatedFormat('d M Y') }}</p>
                    @if (is_numeric($hari))
                        <p class="mt-2 text-sm {{ $hari < 0 ? 'text-rose-300' : 'text-slate-300' }}">
                            {{ $hari < 0 ? 'Terlambat '.(int) abs($hari).' hari' : (int) $hari.' hari lagi' }}
                        </p>
                    @endif
                @else
                    <p class="mt-4 text-2xl font-semibold tracking-tight text-slate-300">—</p>
                @endif
            </article>
        </div>

        <div class="mt-8 grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Ringkasan kamar</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900">Informasi kamar</h3>
                    </div>
                    <a href="{{ route('penghuni.tagihan.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Lihat tagihan</a>
                </div>
                <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-slate-500">Harga / bulan</dt>
                        <dd class="mt-1 text-base font-semibold text-slate-900">Rp {{ number_format($penghuni->kamar?->harga ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-slate-500">Kapasitas</dt>
                        <dd class="mt-1 text-base font-semibold text-slate-900">{{ $penghuni->kamar?->kapasitas ?? '—' }} orang</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 sm:col-span-2">
                        <dt class="text-slate-500">Fasilitas</dt>
                        <dd class="mt-1 text-slate-900">{{ $penghuni->kamar?->fasilitas ?: '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-3xl bg-gradient-to-br from-violet-600 to-indigo-700 p-6 text-white shadow-lg shadow-indigo-950/10">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-100">Tagihan terbaru</p>
                @if ($tagihanTerbaru)
                    <h3 class="mt-2 text-2xl font-semibold tracking-tight">{{ $tagihanTerbaru->labelPeriode() }}</h3>
                    <div class="mt-5 space-y-3 rounded-2xl bg-white/10 p-4 backdrop-blur-sm">
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-violet-100">Jumlah</span>
                            <span class="font-semibold">Rp {{ number_format($tagihanTerbaru->jumlah, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-violet-100">Jatuh tempo</span>
                            <span class="font-semibold">{{ $tagihanTerbaru->jatuh_tempo?->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-violet-100">Status</span>
                            <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide">{{ $tagihanTerbaru->status }}</span>
                        </div>
                    </div>
                    <a href="{{ route('penghuni.tagihan.index') }}" class="mt-5 inline-flex items-center text-sm font-semibold text-white underline-offset-4 hover:underline">Lihat semua tagihan</a>
                @else
                    <p class="mt-4 text-sm text-violet-100">Belum ada tagihan.</p>
                @endif
            </div>
        </div>
    @endif
</x-app-layout>
