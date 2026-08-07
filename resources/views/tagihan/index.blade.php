<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Data Tagihan</h2>
                <p class="mt-1 text-sm text-slate-600">Kelola kewajiban pembayaran dan penerbitan tagihan bulanan</p>
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
                <button type="button" onclick="document.getElementById('generate-tagihan-modal').showModal()" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow hover:bg-indigo-700 transition active:scale-95">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Generate Tagihan</span>
                </button>

                <dialog id="generate-tagihan-modal" class="w-full max-w-sm rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden text-left">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Generate Tagihan Bulanan</h3>
                        <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin melakukan generate tagihan untuk periode {{ $namaBulan[$bulan] ?? $bulan }} {{ $tahun }}?</p>
                        <div class="flex items-center justify-center gap-3">
                            <button type="button" onclick="document.getElementById('generate-tagihan-modal').close()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition active:scale-95">
                                Batal
                            </button>
                            <form method="POST" action="{{ route('tagihan.generate') }}" class="inline">
                                @csrf
                                <input type="hidden" name="bulan" value="{{ $bulan }}">
                                <input type="hidden" name="tahun" value="{{ $tahun }}">
                                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 shadow-sm transition active:scale-95">
                                    Ya, Lanjutkan
                                </button>
                            </form>
                        </div>
                    </div>
                </dialog>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl py-8 space-y-6">

        @if ($jumlahMenungguGenerate > 0)
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50/80 p-4 text-indigo-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm font-medium">
                        Terdapat <strong class="font-bold text-indigo-950">{{ $jumlahMenungguGenerate }} penghuni</strong> yang berada pada kondisi <span class="rounded bg-indigo-200/80 px-2 py-0.5 font-bold text-indigo-900 text-xs uppercase tracking-wide">Menunggu Generate</span> untuk periode {{ $namaBulan[$bulan] ?? $bulan }} {{ $tahun }}.
                    </p>
                </div>
                <button type="button" onclick="document.getElementById('generate-tagihan-modal').showModal()" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 shadow transition active:scale-95 shrink-0">
                    Generate Tagihan Sekarang
                </button>
            </div>
        @endif

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
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">
                                    {{ $t->status === 'lunas' ? '-' : (is_string($t->jatuh_tempo) ? \Carbon\Carbon::parse($t->jatuh_tempo)->format('d/m/Y') : $t->jatuh_tempo?->format('d/m/Y')) }}
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-900">{{ $t->penghuni?->nama }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($t->status === 'menunggu_generate')
                                        <span class="inline-flex rounded-full bg-slate-100 border border-slate-300 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                            Menunggu Generate
                                        </span>
                                    @elseif ($t->status === 'belum_bayar')
                                        <span class="inline-flex rounded-full bg-rose-50 border border-rose-200 px-2.5 py-0.5 text-xs font-semibold text-rose-800">
                                            Belum Dibayar
                                        </span>
                                    @elseif ($t->status === 'menunggu')
                                        <span class="inline-flex rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                                            Menunggu Verifikasi
                                        </span>
                                    @elseif ($t->status === 'ditolak')
                                        <span class="inline-flex rounded-full bg-rose-100 border border-rose-300 px-2.5 py-0.5 text-xs font-semibold text-rose-900">
                                            Ditolak
                                        </span>
                                    @elseif ($t->status === 'lunas')
                                        <span class="inline-flex rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-800">
                                            {{ ucfirst($t->status) }}
                                        </span>
                                    @endif
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
