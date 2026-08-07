<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Tagihan &amp; Pembayaran</h2>
                <p class="text-sm text-slate-500">Daftar tagihan aktif yang perlu diselesaikan</p>
            </div>
            <a href="{{ route('penghuni.riwayat') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition">
                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Lihat Riwayat Pembayaran</span>
            </a>
        </div>
    </x-slot>

    @if (! $penghuni)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900">Akun belum terhubung ke data penghuni.</div>
    @else
        <div>
            <div class="mb-4 flex flex-wrap items-center justify-between gap-4 rounded-xl bg-indigo-50 px-4 py-3 text-sm text-indigo-900 ring-1 ring-indigo-100">
                <div class="flex items-center gap-2">
                    <span class="font-medium">{{ $penghuni->nama }}</span>
                    <span class="rounded-full bg-indigo-200 px-2.5 py-0.5 text-xs font-semibold">Kamar {{ $penghuni->kamar?->nomor_kamar ?? '—' }}</span>
                </div>
                <span class="text-xs text-indigo-700">Tagihan yang sudah lunas otomatis dipindahkan ke menu Riwayat Pembayaran.</span>
            </div>

            @php
                $hasBlockedTagihan = $tagihans->contains(function ($t) {
                    return \App\Http\Controllers\PenghuniTagihanController::hasEarlierUnpaidTagihan($t);
                });
            @endphp

            @if ($hasBlockedTagihan)
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/90 p-4 text-amber-900 flex items-start gap-3 shadow-sm">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white mt-0.5">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="text-xs space-y-1">
                        <p class="font-bold text-amber-950 text-sm">Pembayaran Dilakukan Berurutan (FIFO)</p>
                        <p class="text-amber-800">Pembayaran dilakukan dari tagihan periode paling lama terlebih dahulu. Selesaikan tagihan terlama sebelum dapat membayar tagihan periode berikutnya.</p>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="overflow-x-auto">
                    @php
                        $statusLabels = [
                            'belum_bayar' => 'Belum Dibayar',
                            'menunggu' => 'Menunggu Verifikasi',
                            'ditolak' => 'Ditolak',
                        ];
                        $statusClasses = [
                            'belum_bayar' => 'bg-amber-100 text-amber-800 border border-amber-200',
                            'menunggu' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                            'ditolak' => 'bg-rose-100 text-rose-800 border border-rose-200',
                        ];
                    @endphp
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">No</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">Periode</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">Jumlah</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">Status</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($tagihans as $t)
                                @php
                                    $late = $t->jatuh_tempo && $t->jatuh_tempo->isPast();
                                    $lastPembayaran = $t->pembayaran->sortByDesc('created_at')->first();
                                    $isPendingMidtrans = $t->status === 'menunggu' && $lastPembayaran && $lastPembayaran->metode_pembayaran === 'midtrans' && $lastPembayaran->status === 'menunggu';
                                    $isBlockedByEarlier = \App\Http\Controllers\PenghuniTagihanController::hasEarlierUnpaidTagihan($t);
                                @endphp
                                <tr class="hover:bg-slate-50/50 align-middle {{ $isBlockedByEarlier ? 'bg-slate-50/30' : '' }}">
                                    <td class="px-4 py-3 text-center text-slate-600">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="font-medium text-slate-900">{{ $t->labelPeriode() }}</div>
                                        <div class="text-xs text-slate-500">Kamar {{ $t->kamar?->nomor_kamar }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-600">
                                        {{ $t->jatuh_tempo?->format('d/m/Y') }}
                                        @if ($late)
                                            <span class="ml-1 rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-700">Terlambat</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($isBlockedByEarlier)
                                            <span class="rounded-full px-3 py-0.5 text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                Terkunci (Bayar Tagihan Paling Lama Dulu)
                                            </span>
                                        @elseif ($isPendingMidtrans)
                                            <span class="rounded-full px-3 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                Menunggu Pembayaran
                                            </span>
                                        @else
                                            <span class="rounded-full px-3 py-0.5 text-xs font-semibold {{ $statusClasses[$t->status] ?? 'bg-slate-100' }}">
                                                {{ $statusLabels[$t->status] ?? ucfirst($t->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($isBlockedByEarlier)
                                                <button type="button" disabled title="Anda masih memiliki tagihan pada periode sebelumnya yang belum diselesaikan." class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-400 cursor-not-allowed shadow-none">
                                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    <span>Bayar (Nonaktif)</span>
                                                </button>
                                            @elseif ($t->status === 'belum_bayar' || $t->status === 'ditolak')
                                                <a href="{{ route('penghuni.tagihan.midtrans', $t) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 active:scale-95 shadow-sm transition-all">Midtrans</a>
                                                <a href="{{ route('penghuni.tagihan.bayar', $t) }}" class="inline-flex items-center gap-1 rounded-lg bg-slate-100 border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-200 active:scale-95 transition-all">Other</a>
                                            @elseif ($isPendingMidtrans)
                                                <a href="{{ route('penghuni.tagihan.midtrans', $t) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 active:scale-95 shadow-sm transition-all">Lanjutkan</a>
                                                <a href="{{ route('penghuni.tagihan.batal-midtrans', $t) }}" class="inline-flex items-center gap-1 rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-rose-700 active:scale-95 shadow-sm transition-all">Batal</a>
                                            @else
                                                <span class="text-xs text-slate-400">Menunggu Verifikasi Admin</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <div class="mx-auto max-w-sm text-center">
                                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 mb-3">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <p class="font-semibold text-slate-800">Tidak ada tagihan aktif</p>
                                            <p class="mt-1 text-xs text-slate-500">Seluruh kewajiban pembayaran Anda telah selesai. Pembayaran yang sudah lunas dapat diakses melalui menu <a href="{{ route('penghuni.riwayat') }}" class="font-semibold text-indigo-600 hover:underline">Riwayat Pembayaran</a>.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
