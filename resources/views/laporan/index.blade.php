<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">Laporan keuangan</h2>
    </x-slot>

    <form method="GET" class="mb-6 flex flex-wrap items-end gap-3 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <div>
            <label class="text-xs text-slate-600">Dari</label>
            <input type="date" name="dari" value="{{ $dari->format('Y-m-d') }}" class="mt-1 block rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="text-xs text-slate-600">Sampai</label>
            <input type="date" name="sampai" value="{{ $sampai->format('Y-m-d') }}" class="mt-1 block rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="text-xs text-slate-600">Kamar</label>
            <select name="kamar_id" class="mt-1 block rounded-lg border-slate-300 text-sm">
                <option value="">Semua kamar</option>
                @foreach ($kamars as $k)
                    <option value="{{ $k->id }}" @selected((string) $kamarId === (string) $k->id)>{{ $k->nomor_kamar }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white">Filter</button>
        <a href="{{ route('laporan.export', array_merge(request()->query(), ['tipe' => 'lunas'])) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white">Unduh Laporan Lunas</a>
        <a href="{{ route('laporan.export', array_merge(request()->query(), ['tipe' => 'belum_bayar'])) }}" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white">Unduh Laporan Belum Bayar</a>
        <a href="{{ route('laporan.print', request()->query()) }}" target="_blank" class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-medium text-white">Cetak</a>
    </form>

    <div class="mb-6 rounded-xl bg-indigo-50 p-4 text-sm text-indigo-900 ring-1 ring-indigo-100">
        Periode: {{ $dari->translatedFormat('d M Y') }} — {{ $sampai->translatedFormat('d M Y') }} ·
        Total: <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong> ·
        {{ $rows->count() }} transaksi
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h3 class="text-sm font-semibold text-slate-800">Grafik pemasukan 6 bulan</h3>
            <div class="mt-4 h-56">
                <canvas id="laporanChart"></canvas>
            </div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h3 class="text-sm font-semibold text-slate-800">Ringkasan</h3>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <li>Penghuni aktif: <strong>{{ $penghuniAktif }}</strong></li>
                <li>Kamar terisi: <strong>{{ $kamarTerisi }}</strong> / {{ $totalKamar }}</li>
            </ul>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-100 px-4 py-3">
            <h3 class="text-sm font-semibold">Detail pembayaran (lunas)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left">No.</th>
                        <th class="px-3 py-2 text-left">Tanggal</th>
                        <th class="px-3 py-2 text-left">Penghuni</th>
                        <th class="px-3 py-2 text-left">Kamar</th>
                        <th class="px-3 py-2 text-left">Periode</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2">{{ $loop->iteration }}</td>
                            <td class="px-3 py-2">{{ $r->tanggal_bayar }}</td>
                            <td class="px-3 py-2">{{ $r->penghuni?->nama }}</td>
                            <td class="px-3 py-2">{{ $r->penghuni?->kamar?->nomor_kamar }}</td>
                            <td class="px-3 py-2">{{ $r->tagihan ? $r->tagihan->labelPeriode() : '—' }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($r->jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Tidak ada data dalam periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            new Chart(document.getElementById('laporanChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Pemasukan',
                        data: {!! json_encode($chartValues) !!},
                        backgroundColor: 'rgba(109, 40, 217, 0.7)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });
        </script>
    @endpush
</x-app-layout>
