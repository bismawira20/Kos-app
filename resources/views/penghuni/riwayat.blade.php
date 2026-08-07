<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Riwayat Pembayaran</h2>
                <p class="text-sm text-slate-500">Arsip seluruh transaksi pembayaran yang telah selesai (Lunas)</p>
            </div>
            <a href="{{ route('penghuni.tagihan.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition">
                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                <span>Kembali ke Tagihan Aktif</span>
            </a>
        </div>
    </x-slot>

    @if (! $penghuni)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900">Akun belum terhubung ke data penghuni.</div>
    @else
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-center font-semibold text-slate-700">No</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-700">Tanggal Bayar</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-700">Periode Tagihan</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-700">Nominal</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-700">Metode Pembayaran</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-700">Bukti Pembayaran</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-700">Status</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-700">Kuitansi / Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pembayaran as $p)
                            <tr class="align-middle hover:bg-slate-50/50">
                                <td class="px-4 py-3 text-center text-slate-600">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-center font-medium text-slate-900">
                                    {{ $p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') : $p->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-indigo-900">
                                    {{ $p->tagihan ? $p->tagihan->labelPeriode() : '—' }}
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-slate-900">
                                    Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($p->metode_pembayaran === 'midtrans')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 border border-blue-200 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Midtrans (Online)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                            Manual Transfer
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($p->bukti)
                                        <button type="button" onclick="document.getElementById('bukti-modal-{{ $p->id }}').showModal()" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            <img src="{{ asset('bukti/'.$p->bukti) }}" alt="Bukti" class="h-8 w-8 rounded object-cover ring-1 ring-slate-200">
                                            <span>Lihat Bukti</span>
                                        </button>
                                        <dialog id="bukti-modal-{{ $p->id }}" class="max-w-lg rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50">
                                            <form method="dialog" class="p-4">
                                                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                                    <h3 class="font-semibold text-slate-900 text-sm">Bukti Pembayaran {{ $p->tagihan ? $p->tagihan->labelPeriode() : '' }}</h3>
                                                    <button class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
                                                </div>
                                                <img src="{{ asset('bukti/'.$p->bukti) }}" class="mt-3 max-h-[65vh] w-full rounded-xl object-contain bg-slate-50" alt="Bukti Transfer">
                                                <button class="mt-4 w-full rounded-xl bg-slate-100 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">Tutup</button>
                                            </form>
                                        </dialog>
                                    @elseif ($p->metode_pembayaran === 'midtrans')
                                        <span class="text-xs text-slate-400 font-medium">Otomatis Terverifikasi</span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex rounded-full bg-emerald-50 border border-emerald-200 px-3 py-0.5 text-xs font-bold tracking-wide text-emerald-800">
                                        LUNAS
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($p->tagihan)
                                        <a href="{{ route('penghuni.tagihan.invoice', $p->tagihan) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 border border-indigo-200 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100 active:scale-95 transition-all">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            <span>Unduh Invoice</span>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-500">
                                    <div class="mx-auto max-w-sm text-center">
                                        <p class="font-semibold text-slate-700">Belum ada riwayat pembayaran</p>
                                        <p class="mt-1 text-xs text-slate-500">Transaksi pembayaran yang telah berhasil dan lunas akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-app-layout>
