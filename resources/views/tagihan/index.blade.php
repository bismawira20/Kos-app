<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Data Tagihan</h2>
                <p class="mt-1 text-sm text-slate-600">Generate bulanan atau tambah tagihan manual untuk periode {{ $namaBulan[$bulan] ?? $bulan }} {{ $tahun }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" class="flex gap-2">
                    <select name="bulan" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @selected($bulan === $i)>{{ $namaBulan[$i] ?? $i }}</option>
                        @endfor
                    </select>
                    <select name="tahun" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @for ($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" @selected($tahun === $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
                <a href="{{ route('tagihan.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow hover:bg-indigo-700 transition active:scale-95">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Tagihan</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl py-8 space-y-6">

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-800">Generate tagihan (semua penghuni)</h3>
            <p class="mt-1 text-xs text-slate-500">Membuat tagihan per kamar (harga dari data kamar) jika belum ada untuk periode ini.</p>
            @php
                $maxDays = \Carbon\Carbon::create($tahun, $bulan, 1)->daysInMonth;
            @endphp
            <form method="POST" action="{{ route('tagihan.generate') }}" class="mt-4 flex flex-wrap items-end gap-3">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <div>
                    <label class="text-xs text-slate-600 font-medium">Tanggal jatuh tempo (hari, 1-{{ $maxDays }})</label>
                    <input type="number" name="hari_jatuh_tempo" value="10" min="1" max="{{ $maxDays }}" class="mt-1 block rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600 transition active:scale-95">Generate</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">No</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Kamar</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Harga</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Periode</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Jatuh Tempo</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Penghuni</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Status</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($tagihans as $t)
                            <tr class="border-b border-slate-200 transition hover:bg-slate-50">
                                <td class="px-6 py-4 text-center text-slate-600">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-900">Kamar {{ $t->kamar?->nomor_kamar }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-900">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">{{ $t->labelPeriode() }}</td>
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">{{ $t->status === 'lunas' ? '-' : $t->jatuh_tempo?->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-900">{{ $t->penghuni?->nama }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold border
                                        {{ $t->status === 'lunas' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($t->status === 'menunggu' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-rose-50 text-rose-800 border-rose-200') }}">
                                        {{ ucfirst($t->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2 whitespace-nowrap">
                                        <button type="button" onclick="document.getElementById('delete-tagihan-{{ $t->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-100 px-3 py-1.5 text-sm font-medium text-rose-700 transition hover:bg-rose-200 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>

                                        {{-- Delete Modal --}}
                                        <dialog id="delete-tagihan-{{ $t->id }}" class="w-full max-w-sm rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden">
                                            <div class="p-8 text-center">
                                                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-4 border-amber-400 text-amber-400 mb-6">
                                                    <span class="text-5xl font-light leading-none -mt-1">!</span>
                                                </div>
                                                <h3 class="text-2xl font-bold text-slate-800 tracking-wider uppercase mb-2">DELETE</h3>
                                                <p class="text-slate-600 mb-8">Hapus Data Tagihan : <span class="font-semibold">Kamar {{ $t->kamar?->nomor_kamar }} ({{ $t->labelPeriode() }})</span> ?</p>
                                                <div class="flex items-center justify-center gap-4">
                                                    <form action="{{ route('tagihan.destroy', $t) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded-lg bg-rose-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-rose-700 shadow-md transition active:scale-95">
                                                            Yes, delete!
                                                        </button>
                                                    </form>
                                                    <button type="button" onclick="document.getElementById('delete-tagihan-{{ $t->id }}').close()" class="rounded-lg bg-slate-400 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-500 shadow-md transition active:scale-95">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </dialog>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">Belum ada tagihan untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
