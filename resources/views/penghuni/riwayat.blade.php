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
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Jumlah</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembayaran as $p)
                        <tr>
                            <td class="px-4 py-3">{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $p->tagihan ? $p->tagihan->labelPeriode() : '—' }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $p->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</x-app-layout>
