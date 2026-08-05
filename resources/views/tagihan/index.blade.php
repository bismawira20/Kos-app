<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Data Tagihan</h2>
                <p class="mt-1 text-sm text-slate-600">Generate bulanan untuk periode {{ $namaBulan[$bulan] ?? $bulan }} {{ $tahun }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" class="flex gap-2">
                    <select name="bulan" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @selected($bulan === $i)>{{ $namaBulan[$i] ?? $i }}</option>
                        @endfor
                    </select>
                    <select name="tahun" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @for ($y = now()->year; $y <= now()->year + 3; $y++)
                            <option value="{{ $y }}" @selected($tahun === $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
                <form method="POST" action="{{ route('tagihan.generate') }}">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow hover:bg-indigo-700 transition active:scale-95">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Generate Tagihan</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl py-8 space-y-6">

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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($tagihans as $t)
                            <tr class="border-b border-slate-200 transition hover:bg-slate-50">
                                <td class="px-6 py-4 text-center text-slate-600">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-900">Kamar {{ $t->kamar?->nomor_kamar }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-900">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">
                                     {{ $t->labelPeriode() }}
                                     @if ($t->is_tunggakan)
                                         <span class="ml-1 inline-flex rounded-full bg-rose-100 border border-rose-200 px-2 py-0.5 text-[10px] font-bold text-rose-700 uppercase tracking-wide">
                                             Tunggakan
                                         </span>
                                     @endif
                                 </td>
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">{{ $t->status === 'lunas' ? '-' : $t->jatuh_tempo?->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-900">{{ $t->penghuni?->nama }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold border
                                        {{ $t->status === 'lunas' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($t->status === 'menunggu' ? 'bg-amber-50 text-amber-800 border-amber-200' : ($t->status === 'menunggu_generate' ? 'bg-slate-100 text-slate-800 border-slate-200' : 'bg-red-50 text-red-800 border-red-200')) }}">
                                        @if ($t->status === 'belum_bayar')
                                            Belum Dibayar
                                        @elseif ($t->status === 'menunggu_generate')
                                            Menunggu Generate
                                        @elseif ($t->status === 'menunggu')
                                            Menunggu Verifikasi
                                        @else
                                            {{ ucfirst($t->status) }}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">Belum ada tagihan untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
