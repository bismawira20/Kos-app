<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-slate-800">Tagihan &amp; pembayaran</h2>
            <p class="text-sm text-slate-500">Daftar tagihan Anda</p>
        </div>
    </x-slot>

    @if (! $penghuni)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-sm">Akun belum terhubung ke data penghuni.</div>
    @else
        <div>
            <div class="mb-6 rounded-xl bg-indigo-50 px-4 py-3 text-sm text-indigo-900 ring-1 ring-indigo-100">
                <span class="font-medium">{{ $penghuni->nama }}</span>
                <span class="ml-2 rounded-full bg-indigo-200 px-2 py-0.5 text-xs">Kamar {{ $penghuni->kamar?->nomor_kamar ?? '—' }}</span>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">No</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">Periode</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">Jatuh tempo</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">Jumlah</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">Status</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($tagihans as $t)
                                @php
                                    $late = $t->jatuh_tempo && $t->jatuh_tempo->isPast() && $t->status !== 'lunas';
                                    $lastPembayaran = $t->pembayaran->sortByDesc('created_at')->first();
                                    $isPendingMidtrans = $t->status === 'menunggu' && $lastPembayaran && $lastPembayaran->metode_pembayaran === 'midtrans' && $lastPembayaran->status === 'menunggu';
                                @endphp
                                <tr class="hover:bg-slate-50/50 align-middle">
                                    <td class="px-4 py-3 text-center text-slate-600">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="font-medium text-slate-900">{{ $t->labelPeriode() }}</div>
                                        <div class="text-xs text-slate-500">Kamar {{ $t->kamar?->nomor_kamar }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-600">
                                        @if ($t->status === 'lunas')
                                            -
                                        @else
                                            {{ $t->jatuh_tempo?->format('d/m/Y') }}
                                            @if ($late)
                                                <span class="ml-1 text-xs text-red-600 font-semibold">Terlambat</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-950">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="rounded-full px-3 py-0.5 text-xs font-semibold
                                            {{ $t->status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : ($t->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                            {{ strtoupper(str_replace('_', ' ', $t->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($t->status === 'lunas')
                                                <a href="{{ route('penghuni.tagihan.invoice', $t) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-100 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-200 active:scale-95 transition-all">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                    <span>Unduh Invoice</span>
                                                </a>
                                            @elseif ($t->status === 'belum_bayar')
                                                <a href="{{ route('penghuni.tagihan.midtrans', $t) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 active:scale-95 shadow-sm transition-all">Midtrans</a>
                                                <a href="{{ route('penghuni.tagihan.bayar', $t) }}" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 active:scale-95 shadow-sm transition-all">Manual</a>
                                            @elseif ($isPendingMidtrans)
                                                <a href="{{ route('penghuni.tagihan.midtrans', $t) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 active:scale-95 shadow-sm transition-all">Lanjutkan</a>
                                                <a href="{{ route('penghuni.tagihan.batal-midtrans', $t) }}" class="inline-flex items-center gap-1 rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-rose-700 active:scale-95 shadow-sm transition-all">Batal</a>
                                            @else
                                                <span class="text-xs text-slate-400">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada tagihan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
