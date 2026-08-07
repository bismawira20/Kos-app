<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-violet-500">Penghuni Dashboard</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                    Selamat datang, {{ $penghuni ? $penghuni->nama : Auth::user()->name }}
                </h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">{{ $penghuni ? 'Pantau tagihan aktif, sisa waktu jatuh tempo, dan status laporan kendala Anda.' : 'Akun Anda menunggu aktivasi dari admin.' }}</p>
            </div>
            @if ($penghuni)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('penghuni.tagihan.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition active:scale-95">Bayar Tagihan</a>
                    <a href="{{ route('penghuni.kendala.create') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition active:scale-95">Laporkan Kendala</a>
                </div>
            @endif
        </div>
    </x-slot>

    @if (! $penghuni)
        <div class="mx-auto max-w-lg py-12">
            <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-8 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-amber-600 mb-5">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-amber-900">Menunggu Aktivasi Admin</h3>
                <p class="mt-3 text-sm leading-relaxed text-amber-800">
                    Akun Anda telah berhasil terdaftar. Namun, admin belum menghubungkan akun Anda dengan data penghuni kos.
                </p>
                <p class="mt-2 text-sm leading-relaxed text-amber-700">
                    Silakan hubungi admin agar data Anda segera diaktifkan. Setelah diaktifkan, seluruh fitur seperti tagihan, pembayaran, dan laporan kendala akan dapat diakses.
                </p>
            </div>
        </div>
    @else
        @php
            $hari = $stats['hari_jatuh_tempo'] ?? null;
        @endphp
        
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <!-- Card 1: Kamar Saya -->
            <article class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-700 via-indigo-800 to-violet-900 p-5 text-white shadow-lg shadow-indigo-950/10">
                <div class="absolute right-0 top-0 -mr-4 -mt-4 h-20 w-20 rounded-full bg-white/10 blur-xl"></div>
                
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-200">Kamar Saya</p>
                        <p class="mt-4 text-4xl font-extrabold tracking-tight">{{ $penghuni->kamar?->nomor_kamar ?? '—' }}</p>
                        @if ($penghuni->kamar?->tipeKamar)
                            <span class="mt-3 inline-flex items-center gap-1 rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-medium text-white backdrop-blur-sm">
                                Tipe: {{ $penghuni->kamar->tipeKamar->nama }}
                            </span>
                        @endif
                    </div>
                    <div class="rounded-2xl bg-white/10 p-2.5 backdrop-blur-md">
                        <svg class="h-5 w-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </article>

            <!-- Card 2: Tagihan Aktif -->
            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tagihan Aktif</p>
                        <div class="rounded-xl bg-amber-50 p-2 text-amber-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $tagihanAktif ? 'Rp '.number_format($tagihanAktif->jumlah, 0, ',', '.') : 'Rp 0' }}
                    </p>
                </div>
                <div class="mt-4">
                    @if ($tagihanAktif)
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $tagihanAktif->status === 'menunggu' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                            {{ $tagihanAktif->status === 'menunggu' ? 'Menunggu Verifikasi' : 'Belum Dibayar' }}
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-emerald-100 border border-emerald-200 px-3 py-1 text-xs font-semibold text-emerald-800">
                            Tidak Ada Tagihan Aktif
                        </span>
                    @endif
                </div>
            </article>

            <!-- Card 3: Jatuh Tempo -->
            <article class="rounded-3xl bg-slate-950 p-5 text-white shadow-lg shadow-slate-950/20 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Jatuh Tempo</p>
                        <div class="rounded-xl bg-white/10 p-2 text-slate-300">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    </div>
                    @if ($tagihanAktif && $tagihanAktif->jatuh_tempo)
                        <p class="mt-3 text-2xl font-bold tracking-tight">{{ $tagihanAktif->jatuh_tempo->translatedFormat('d M Y') }}</p>
                    @else
                        <p class="mt-3 text-2xl font-bold tracking-tight text-slate-400">—</p>
                    @endif
                </div>
                <div class="mt-4">
                    @if ($tagihanAktif && $tagihanAktif->jatuh_tempo)
                        @if (is_numeric($hari))
                            <p class="text-xs font-semibold {{ $hari < 0 ? 'text-rose-400' : ($hari <= 3 ? 'text-amber-300' : 'text-slate-300') }}">
                                @if ($hari < 0)
                                    Terlambat {{ (int) abs($hari) }} hari
                                @elseif ($hari === 0)
                                    Jatuh tempo hari ini
                                @else
                                    Sisa {{ (int) $hari }} hari lagi
                                @endif
                            </p>
                        @endif
                    @else
                        <p class="text-xs font-medium text-emerald-400">Semua tagihan lunas</p>
                    @endif
                </div>
            </article>

            <!-- Card 4: Laporan Kendala Terakhir -->
            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Laporan Kendala Terakhir</p>
                        <div class="rounded-xl bg-violet-50 p-2 text-violet-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                    </div>
                    @if ($laporanTerakhir)
                        <p class="mt-3 text-sm font-semibold text-slate-800 line-clamp-1" title="{{ $laporanTerakhir->deskripsi }}">
                            {{ $laporanTerakhir->deskripsi }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $laporanTerakhir->created_at->translatedFormat('d M Y') }}</p>
                    @else
                        <p class="mt-3 text-lg font-semibold text-slate-400">Belum ada laporan</p>
                    @endif
                </div>
                <div class="mt-4">
                    @if ($laporanTerakhir)
                        @php
                            $statusClasses = [
                                'menunggu' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'proses' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'diperbaiki' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
                            ];
                            $statusLabels = [
                                'menunggu' => 'Menunggu Tanggapan',
                                'proses' => 'Sedang Dikerjakan',
                                'diperbaiki' => 'Menunggu Konfirmasi Anda',
                                'selesai' => 'Selesai',
                                'ditolak' => 'Ditolak',
                            ];
                        @endphp
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold border {{ $statusClasses[$laporanTerakhir->status] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ $statusLabels[$laporanTerakhir->status] ?? ucfirst($laporanTerakhir->status) }}
                        </span>
                    @else
                        <a href="{{ route('penghuni.kendala.create') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Buat laporan baru &rarr;</a>
                    @endif
                </div>
            </article>
        </div>

        <div class="mt-8 grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <!-- Ringkasan Kamar -->
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Ringkasan Kamar</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900">Informasi Kamar</h3>
                    </div>
                    <a href="{{ route('penghuni.tagihan.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Lihat Tagihan</a>
                </div>

                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <!-- Harga / Bulan Card -->
                    <div class="flex items-center gap-4 rounded-2xl bg-slate-50 p-4 transition-all hover:bg-slate-100/70">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Harga / Bulan</dt>
                            <dd class="mt-0.5 text-base font-bold text-slate-950">Rp {{ number_format($penghuni->kamar?->harga ?? 0, 0, ',', '.') }}</dd>
                        </div>
                    </div>

                    <!-- Status Card (Updated per Rule 4) -->
                    <div class="flex items-center gap-4 rounded-2xl bg-slate-50 p-4 transition-all hover:bg-slate-100/70">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Status</dt>
                            <dd class="mt-0.5 text-sm font-bold text-emerald-700">Aktif</dd>
                        </div>
                    </div>

                    <!-- Mulai Sewa Card -->
                    <div class="flex items-center gap-4 rounded-2xl bg-slate-50 p-4 transition-all hover:bg-slate-100/70">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Mulai Sewa</dt>
                            <dd class="mt-0.5 text-sm font-bold text-slate-950">
                                {{ $penghuni->tanggal_masuk ? $penghuni->tanggal_masuk->format('d M Y') : '—' }}
                            </dd>
                        </div>
                    </div>

                    <!-- Akhir Sewa Card -->
                    <div class="flex items-center gap-4 rounded-2xl bg-slate-50 p-4 transition-all hover:bg-slate-100/70">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Akhir Sewa</dt>
                            <dd class="mt-0.5 text-sm font-bold text-slate-950">
                                {{ $penghuni->tanggal_selesai ? $penghuni->tanggal_selesai->format('d M Y') : '—' }}
                            </dd>
                        </div>
                    </div>
                </dl>
            </div>

            <!-- Tagihan Terbaru Card -->
            <div class="rounded-3xl bg-gradient-to-br from-violet-600 to-indigo-700 p-6 text-white shadow-lg shadow-indigo-950/10 flex flex-col justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-100">Tagihan Terbaru</p>
                    @if ($tagihanTerbaru)
                        <h3 class="mt-2 text-2xl font-semibold tracking-tight">{{ $tagihanTerbaru->labelPeriode() }}</h3>
                        <div class="mt-5 space-y-3 rounded-2xl bg-white/10 p-4 backdrop-blur-sm">
                            <div class="flex items-center justify-between gap-4 text-sm">
                                <span class="text-violet-100">Jumlah</span>
                                <span class="font-semibold">Rp {{ number_format($tagihanTerbaru->jumlah, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 text-sm">
                                <span class="text-violet-100">Jatuh Tempo</span>
                                <span class="font-semibold">{{ $tagihanTerbaru->status === 'lunas' ? '-' : $tagihanTerbaru->jatuh_tempo?->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 text-sm">
                                <span class="text-violet-100">Status</span>
                                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                                    {{ $tagihanTerbaru->status === 'lunas' ? 'Lunas' : ($tagihanTerbaru->status === 'menunggu' ? 'Menunggu Verifikasi' : 'Belum Dibayar') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-violet-100">Belum ada tagihan.</p>
                    @endif
                </div>
                <div class="mt-5">
                    <a href="{{ route('penghuni.tagihan.index') }}" class="inline-flex items-center text-sm font-semibold text-white underline-offset-4 hover:underline">Lihat semua tagihan &rarr;</a>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
