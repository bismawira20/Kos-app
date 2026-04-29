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
        <div class="mb-6 rounded-xl bg-indigo-50 px-4 py-3 text-sm text-indigo-900 ring-1 ring-indigo-100">
            <span class="font-medium">{{ $penghuni->nama }}</span>
            <span class="ml-2 rounded-full bg-indigo-200 px-2 py-0.5 text-xs">Kamar {{ $penghuni->kamar?->nomor_kamar ?? '—' }}</span>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Periode</th>
                            <th class="px-4 py-3 text-left">Jatuh tempo</th>
                            <th class="px-4 py-3 text-left">Jumlah</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tagihans as $t)
                            @php
                                $late = $t->jatuh_tempo && $t->jatuh_tempo->isPast() && $t->status !== 'lunas';
                            @endphp
                            <tr>
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $t->labelPeriode() }}</div>
                                    <div class="text-xs text-slate-500">Kamar {{ $t->kamar?->nomor_kamar }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $t->jatuh_tempo?->format('d/m/Y') }}
                                    @if ($late)
                                        <span class="ml-1 text-xs text-red-600">Terlambat</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ $t->status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : ($t->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ strtoupper(str_replace('_', ' ', $t->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($t->status === 'belum_bayar')
                                        <a href="{{ route('penghuni.tagihan.bayar', $t) }}" class="inline-block rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700">Bayar</a>
                                    @elseif ($t->status === 'lunas')
                                        <span class="text-xs text-slate-500">Lunas</span>
                                    @else
                                        <span class="text-xs text-amber-700">Menunggu verifikasi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada tagihan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-app-layout>
