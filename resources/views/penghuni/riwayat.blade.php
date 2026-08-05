<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">Riwayat pembayaran</h2>
    </x-slot>

    @if (! $penghuni)
        <p class="text-slate-500">Akun belum terhubung.</p>
    @else
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Tanggal</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Periode</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Jumlah</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pembayaran as $p)
                        <tr>
                            <td class="px-4 py-3 text-center text-slate-600">{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-center text-slate-900 font-medium">{{ $p->tagihan ? $p->tagihan->labelPeriode() : '—' }}</td>
                            <td class="px-4 py-3 text-center text-slate-700 font-semibold">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($p->status === 'lunas')
                                    <span class="inline-flex rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold tracking-wide text-emerald-800 uppercase shadow-sm">
                                        LUNAS
                                    </span>
                                @elseif($p->status === 'menunggu')
                                    <span class="inline-flex rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-bold tracking-wide text-amber-800 uppercase shadow-sm">
                                        MENUNGGU
                                    </span>
                                @elseif($p->status === 'batal')
                                    <span class="inline-flex rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-bold tracking-wide text-slate-700 uppercase shadow-sm">
                                        BATAL
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-rose-50 border border-rose-200 px-3 py-1 text-xs font-bold tracking-wide text-rose-800 uppercase shadow-sm">
                                        {{ strtoupper($p->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-slate-600 max-w-[180px] truncate" title="{{ $p->admin_komentar }}">{{ $p->admin_komentar ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</x-app-layout>
