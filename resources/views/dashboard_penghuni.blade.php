<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-slate-800">
                Selamat datang{{ $penghuni ? ', '.$penghuni->nama : '' }}
            </h2>
            <p class="mt-1 text-sm text-slate-500">Ringkasan informasi dan tagihan Anda</p>
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
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium text-slate-500">Kamar saya</p>
                <p class="mt-2 text-2xl font-bold text-indigo-700">{{ $penghuni->kamar?->nomor_kamar ?? '—' }}</p>
                <p class="text-xs text-slate-500">Lantai {{ $penghuni->kamar?->lantai ?? '—' }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium text-slate-500">Tagihan bulan ini</p>
                <p class="mt-2 text-xl font-bold text-slate-900">
                    {{ $tb ? 'Rp '.number_format($tb->jumlah, 0, ',', '.') : '—' }}
                </p>
                @if ($tb)
                    <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-medium
                        {{ $tb->status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : ($tb->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                        {{ $tb->status === 'lunas' ? 'Lunas' : ($tb->status === 'menunggu' ? 'Menunggu verifikasi' : 'Belum bayar') }}
                    </span>
                @endif
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium text-slate-500">Tagihan tertunggak</p>
                <p class="mt-2 text-3xl font-bold text-red-600">{{ $stats['tunggakan'] ?? 0 }}</p>
                <p class="text-xs text-slate-500">Jatuh tempo lewat</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium text-slate-500">Jatuh tempo</p>
                @if ($tb && $tb->jatuh_tempo)
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $tb->jatuh_tempo->translatedFormat('d M Y') }}</p>
                    @if (is_numeric($hari))
                        <p class="text-xs {{ $hari < 0 ? 'text-red-600' : 'text-slate-500' }}">
                            {{ $hari < 0 ? 'Terlambat '.(int) abs($hari).' hari' : (int) $hari.' hari lagi' }}
                        </p>
                    @endif
                @else
                    <p class="mt-2 text-slate-500">—</p>
                @endif
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="font-semibold text-slate-800">Informasi kamar</h3>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Harga / bulan</dt><dd>Rp {{ number_format($penghuni->kamar?->harga ?? 0, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Kapasitas</dt><dd>{{ $penghuni->kamar?->kapasitas ?? '—' }} orang</dd></div>
                    <div class="mt-3 border-t border-slate-100 pt-3">
                        <dt class="text-slate-500">Fasilitas</dt>
                        <dd class="mt-1 text-slate-700">{{ $penghuni->kamar?->fasilitas ?: '—' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="font-semibold text-slate-800">Tagihan terbaru</h3>
                @if ($tagihanTerbaru)
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Periode</dt><dd>{{ $tagihanTerbaru->labelPeriode() }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Jumlah</dt><dd>Rp {{ number_format($tagihanTerbaru->jumlah, 0, ',', '.') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Jatuh tempo</dt><dd>{{ $tagihanTerbaru->jatuh_tempo?->format('d/m/Y') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Status</dt>
                            <dd>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium">{{ $tagihanTerbaru->status }}</span>
                            </dd>
                        </div>
                    </dl>
                    <a href="{{ route('penghuni.tagihan.index') }}" class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:underline">Lihat semua tagihan →</a>
                @else
                    <p class="mt-4 text-sm text-slate-500">Belum ada tagihan.</p>
                @endif
            </div>
        </div>
    @endif
</x-app-layout>
