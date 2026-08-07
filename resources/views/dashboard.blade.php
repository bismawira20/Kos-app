<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-indigo-500">Admin Control Center</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Selamat Datang, Admin</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">Pantau okupansi, pendapatan, penerbitan tagihan, verifikasi pembayaran, dan laporan kendala dari satu panel.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($jumlahMenungguGenerate > 0)
                    <a href="{{ route('tagihan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition active:scale-95">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Generate Tagihan ({{ $jumlahMenungguGenerate }})</span>
                    </a>
                @endif
                <a href="{{ route('pembayaran.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition active:scale-95">Verifikasi Pembayaran</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Grid Ringkasan Utama (6 Cards) -->
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <!-- Card 1: Total Kamar -->
            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Total Kamar</p>
                <div class="mt-4 flex items-end justify-between gap-3">
                    <p class="text-4xl font-semibold tracking-tight text-slate-900">{{ $totalKamar }}</p>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $kamarTerisi }} Terisi</span>
                </div>
            </article>

            <!-- Card 2: Kamar Kosong -->
            <article class="group rounded-3xl bg-gradient-to-br from-indigo-700 to-violet-900 p-[1px] shadow-lg shadow-indigo-950/10">
                <div class="rounded-[1.45rem] bg-white/95 p-5 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Kamar Kosong</p>
                    <div class="mt-4 flex items-end justify-between gap-3">
                        <p class="text-4xl font-semibold tracking-tight text-slate-900">{{ $kamarKosong }}</p>
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $occupancy }}% Okupansi</span>
                    </div>
                </div>
            </article>

            <!-- Card 3: Total Penghuni -->
            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Total Penghuni</p>
                <div class="mt-4 flex items-end justify-between gap-3">
                    <p class="text-4xl font-semibold tracking-tight text-slate-900">{{ $totalPenghuni }}</p>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Orang</span>
                </div>
            </article>

            <!-- Card 4: Laporan Kendala Baru -->
            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Laporan Kendala Baru</p>
                <div class="mt-4 flex items-end justify-between gap-3">
                    <p class="text-4xl font-semibold tracking-tight text-slate-900">{{ $jumlahKendalaBaru }}</p>
                    <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">Menunggu</span>
                </div>
            </article>

            <!-- Card 5: Pendapatan / Pemasukan Bulan Ini -->
            <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Pendapatan Bulan Ini</p>
                <p class="mt-4 text-3xl font-semibold tracking-tight text-slate-900">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ $namaBulan[(int) $bulan] ?? '' }} {{ $tahun }}</p>
            </article>

            <!-- Card 6: Menunggu Verifikasi -->
            <article class="rounded-3xl bg-slate-950 p-5 text-white shadow-lg shadow-slate-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Menunggu Verifikasi</p>
                <p class="mt-4 text-4xl font-semibold tracking-tight text-white">{{ $menungguVerifikasi }}</p>
                <p class="mt-2 text-xs text-slate-400">Bukti Pembayaran</p>
            </article>
        </section>

        <!-- Monitoring Section: Grafik & Ringkasan Cepat -->
        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.9fr]">
            <!-- Grafik Pembayaran Lunas -->
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Monitoring</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900">Grafik Pembayaran Lunas</h3>
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
                <div class="mt-6 h-56 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100 p-4">
                    <canvas id="chartPembayaran"></canvas>
                </div>
            </div>

            <!-- Ringkasan Cepat -->
            <div class="space-y-6">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Ringkasan Cepat</p>
                    <ul class="mt-4 space-y-3 text-sm">
                        <!-- 1. Total Penghuni -->
                        <li class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="font-medium text-slate-700">Total Penghuni</span>
                            <span class="font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full text-xs border border-emerald-200">
                                {{ $totalPenghuni }} Orang
                            </span>
                        </li>

                        <!-- 2. Kamar Kosong -->
                        <li class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="font-medium text-slate-700">Kamar Kosong</span>
                            <span class="font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full text-xs border border-indigo-200">
                                {{ $kamarKosong }} Kamar
                            </span>
                        </li>

                        <!-- 3. Menunggu Generate -->
                        <li class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="font-medium text-slate-700">Menunggu Generate</span>
                            <span class="font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full text-xs border border-indigo-200">
                                {{ $jumlahMenungguGenerate }} Penghuni
                            </span>
                        </li>

                        <!-- 4. Menunggu Verifikasi -->
                        <li class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="font-medium text-slate-700">Menunggu Verifikasi</span>
                            <span class="font-bold text-amber-700 bg-amber-50 px-3 py-1 rounded-full text-xs border border-amber-200">
                                {{ $menungguVerifikasi }} Transaksi
                            </span>
                        </li>

                        <!-- 5. Pendapatan Bulan Ini -->
                        <li class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="font-medium text-slate-700">Pendapatan Bulan Ini</span>
                            <span class="font-bold text-amber-800 bg-amber-50 px-3 py-1 rounded-full text-xs border border-amber-200">
                                Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}
                            </span>
                        </li>

                        <!-- 6. Laporan Kendala Baru -->
                        <li class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <span class="font-medium text-slate-700">Laporan Kendala Baru</span>
                            <span class="font-bold text-violet-700 bg-violet-50 px-3 py-1 rounded-full text-xs border border-violet-200">
                                {{ $jumlahKendalaBaru }} Laporan
                            </span>
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
