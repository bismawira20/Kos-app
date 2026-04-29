<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Data tagihan {{ $namaBulan[$bulan] ?? $bulan }} {{ $tahun }}</h2>
                <p class="text-sm text-slate-500">Generate bulanan atau tambah tagihan manual</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="GET" class="flex gap-2">
                    <select name="bulan" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @selected($bulan === $i)>{{ $namaBulan[$i] ?? $i }}</option>
                        @endfor
                    </select>
                    <select name="tahun" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm">
                        @for ($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" @selected($tahun === $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
                <a href="{{ route('tagihan.create') }}" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">+ Tagihan</a>
            </div>
        </div>
    </x-slot>

    <div class="mb-6 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <h3 class="text-sm font-semibold text-slate-800">Generate tagihan (semua penghuni)</h3>
        <p class="mt-1 text-xs text-slate-500">Membuat tagihan per kamar (harga dari data kamar) jika belum ada untuk periode ini.</p>
        <form method="POST" action="{{ route('tagihan.generate') }}" class="mt-4 flex flex-wrap items-end gap-3">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <div>
                <label class="text-xs text-slate-600">Tanggal jatuh tempo (hari)</label>
                <input type="number" name="hari_jatuh_tempo" value="10" min="1" max="28" class="mt-1 rounded-lg border-slate-300 text-sm" required>
            </div>
            <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">Generate</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">No</th>
                        <th class="px-4 py-3 text-left">Kamar</th>
                        <th class="px-4 py-3 text-left">Harga</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Jatuh tempo</th>
                        <th class="px-4 py-3 text-left">Penghuni</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tagihans as $t)
                        <tr>
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium">{{ $t->kamar?->nomor_kamar }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $t->labelPeriode() }}</td>
                            <td class="px-4 py-3">{{ $t->jatuh_tempo?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $t->penghuni?->nama }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                    {{ $t->status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : ($t->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('tagihan.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tagihan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">Belum ada tagihan untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
