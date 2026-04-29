<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-indigo-500">Admin control center</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Selamat datang, admin</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">Pantau okupansi, pendapatan, verifikasi pembayaran, dan transaksi operasional dari satu panel.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('transaksi-operasional.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Tambah transaksi</a>
                <a href="{{ route('pembayaran.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Verifikasi pembayaran</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="group rounded-3xl bg-gradient-to-br from-indigo-700 to-violet-900 p-[1px] shadow-lg shadow-indigo-950/10">
                <div class="rounded-[1.45rem] bg-white/95 p-5 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Kamar tersisa</p>
                    <div class="mt-4 flex items-end justify-between gap-3">
                        <p class="text-4xl font-semibold tracking-tight text-slate-900">{{ $kamarKosong }}</p>
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $totalKamar }} total</span>
                    </div>
                </div>
            </article>

            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Penghuni aktif</p>
                <div class="mt-4 flex items-end justify-between gap-3">
                    <p class="text-4xl font-semibold tracking-tight text-slate-900">{{ $totalPenghuni }}</p>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $kamarTerisi }} terisi</span>
                </div>
            </article>

            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Pemasukan bulan ini</p>
                <p class="mt-4 text-3xl font-semibold tracking-tight text-slate-900">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $namaBulan[(int) $bulan] ?? '' }} {{ $tahun }}</p>
            </article>

            <article class="rounded-3xl bg-slate-950 p-5 text-white shadow-lg shadow-slate-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Menunggu verifikasi</p>
                <p class="mt-4 text-4xl font-semibold tracking-tight text-white">{{ $menungguVerifikasi }}</p>
                <p class="mt-2 text-xs text-slate-400">Bukti pembayaran</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.9fr]">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Monitoring</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900">Grafik pembayaran lunas</h3>
                    </div>
                    <form method="GET" class="flex gap-2">
                        <select name="bulan" onchange="this.form.submit()" class="rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" @selected((int) $bulan === $i)>{{ $namaBulan[$i] ?? $i }}</option>
                            @endfor
                        </select>
                        <select name="tahun" onchange="this.form.submit()" class="rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" @selected((int) $tahun === $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                </div>
                <div class="mt-6 h-72 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100 p-4">
                    <canvas id="chartPembayaran"></canvas>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl bg-gradient-to-br from-indigo-700 to-violet-900 p-6 text-white shadow-lg">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">Keuangan operasional</p>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl bg-white/10 p-4 backdrop-blur-sm">
                            <p class="text-xs text-indigo-200">Pemasukan operasional</p>
                            <p class="mt-1 text-2xl font-semibold">Rp {{ number_format($pemasukanOperasional, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4 backdrop-blur-sm">
                            <p class="text-xs text-indigo-200">Pengeluaran operasional</p>
                            <p class="mt-1 text-2xl font-semibold">Rp {{ number_format($pengeluaranOperasional, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-2xl bg-white p-4 text-slate-900">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Saldo operasional</p>
                            <p class="mt-1 text-3xl font-semibold">Rp {{ number_format($saldoOperasional, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Cepat cek</p>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        <li class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span>Tagihan belum lunas</span>
                            <span class="font-semibold text-slate-900">{{ $tagihanBelumLunasBulanIni }}</span>
                        </li>
                        <li class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span>Tingkat okupansi</span>
                            <span class="font-semibold text-indigo-700">{{ $occupancy }}%</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
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
