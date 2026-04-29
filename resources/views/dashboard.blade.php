<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-slate-800">Selamat datang, admin</h2>
            <p class="mt-1 text-sm text-slate-500">Ringkasan aktivitas dan statistik kos</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Kamar tersisa</p>
                <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $kamarKosong }}</p>
                <p class="mt-1 text-xs text-slate-500">dari {{ $totalKamar }} total kamar</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Penghuni aktif</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $totalPenghuni }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $kamarTerisi }} kamar terisi</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Pemasukan bulan ini</p>
                <p class="mt-2 text-2xl font-bold text-violet-700">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Menunggu verifikasi</p>
                <p class="mt-2 text-3xl font-bold text-amber-600">{{ $menungguVerifikasi }}</p>
                <p class="mt-1 text-xs text-slate-500">Bukti pembayaran</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:col-span-1">
                <p class="text-sm font-medium text-slate-700">Statistik cepat</p>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li class="flex justify-between"><span>Tagihan belum lunas (bulan ini)</span><span class="font-semibold text-slate-900">{{ $tagihanBelumLunasBulanIni }}</span></li>
                    <li class="flex justify-between"><span>Tingkat okupansi</span><span class="font-semibold text-indigo-600">{{ $occupancy }}%</span></li>
                </ul>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800">Grafik pembayaran lunas</h3>
                        <p class="text-xs text-slate-500">Frekuensi per hari dalam bulan dipilih</p>
                    </div>
                    <form method="GET" class="flex flex-wrap gap-2">
                        <select name="bulan" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" @selected((int) $bulan === $i)>{{ $namaBulan[$i] ?? $i }}</option>
                            @endfor
                        </select>
                        <select name="tahun" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm">
                            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" @selected((int) $tahun === $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                </div>
                <div class="mt-4 h-64">
                    <canvas id="chartPembayaran"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const labels = {!! json_encode($chart->keys()->values()) !!};
            const values = {!! json_encode($chart->values()) !!};
            new Chart(document.getElementById('chartPembayaran'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pembayaran lunas',
                        data: values,
                        borderColor: 'rgb(109, 40, 217)',
                        backgroundColor: 'rgba(109, 40, 217, 0.08)',
                        fill: true,
                        tension: 0.25,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        </script>
    @endpush
</x-app-layout>
