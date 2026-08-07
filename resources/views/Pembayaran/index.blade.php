<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Verifikasi &amp; Riwayat Pembayaran</h2>
                <p class="text-sm text-slate-500">Kelola verifikasi pembayaran manual dan arsip transaksi lunas</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pembayaran.index', ['filter' => 'menunggu']) }}" class="rounded-lg px-3 py-2 text-xs font-semibold {{ ($filter ?? 'menunggu') === 'menunggu' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Menunggu Verifikasi
                </a>
                <a href="{{ route('pembayaran.index', ['filter' => 'lunas']) }}" class="rounded-lg px-3 py-2 text-xs font-semibold {{ ($filter ?? '') === 'lunas' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Riwayat Lunas
                </a>
                <a href="{{ route('pembayaran.index', ['filter' => 'semua']) }}" class="rounded-lg px-3 py-2 text-xs font-semibold {{ ($filter ?? '') === 'semua' ? 'bg-indigo-100 text-indigo-900 border border-indigo-300' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Semua Transaksi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-3 text-center">No</th>
                        <th class="px-3 py-3 text-center">Tanggal Bayar</th>
                        <th class="px-3 py-3 text-center">Penghuni</th>
                        <th class="px-3 py-3 text-center">Kamar</th>
                        <th class="px-3 py-3 text-center">Periode Tagihan</th>
                        <th class="px-3 py-3 text-center">Jumlah</th>
                        <th class="px-3 py-3 text-center">Metode</th>
                        <th class="px-3 py-3 text-center">Bukti Pembayaran</th>
                        <th class="px-3 py-3 text-center">Status</th>
                        <th class="px-3 py-3 text-center">Aksi / Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pembayaran as $p)
                        <tr class="align-middle hover:bg-slate-50/50">
                            <td class="px-3 py-3 text-center text-slate-600">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 text-center whitespace-nowrap text-slate-700">
                                {{ $p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') : $p->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-3 py-3 text-center font-medium text-slate-900">{{ $p->penghuni?->nama }}</td>
                            <td class="px-3 py-3 text-center">
                                <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-800">
                                    {{ $p->penghuni?->kamar?->nomor_kamar ?? '—' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center text-xs font-semibold text-indigo-900">
                                {{ $p->tagihan ? $p->tagihan->labelPeriode() : '—' }}
                            </td>
                            <td class="px-3 py-3 text-center font-bold text-slate-900">
                                Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if ($p->metode_pembayaran === 'midtrans')
                                    <span class="inline-flex rounded-full bg-blue-50 border border-blue-200 px-2 py-0.5 text-[11px] font-semibold text-blue-700">Midtrans</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 border border-slate-200 px-2 py-0.5 text-[11px] font-semibold text-slate-700">Manual</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex justify-center">
                                    @if ($p->bukti)
                                        <button type="button" onclick="document.getElementById('bukti-{{ $p->id }}').showModal()" class="block">
                                            <img src="{{ asset('bukti/'.$p->bukti) }}" alt="" class="h-10 w-10 rounded object-cover ring-1 ring-slate-200 hover:scale-105 transition">
                                        </button>
                                        <dialog id="bukti-{{ $p->id }}" class="max-w-lg rounded-xl p-0 shadow-2xl backdrop:bg-slate-900/50">
                                            <form method="dialog" class="p-4">
                                                <img src="{{ asset('bukti/'.$p->bukti) }}" class="max-h-[70vh] w-full rounded-lg object-contain" alt="Bukti">
                                                <button class="mt-3 w-full rounded-lg bg-slate-200 py-2 text-xs font-bold text-slate-700">Tutup</button>
                                            </form>
                                        </dialog>
                                    @elseif ($p->metode_pembayaran === 'midtrans')
                                        <span class="text-xs text-slate-400 font-medium">Otomatis Midtrans</span>
                                    @else
                                        —
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                @php
                                    $cls = match ($p->status) {
                                        'lunas' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        'menunggu' => 'bg-amber-100 text-amber-850 border border-amber-200',
                                        'ditolak' => 'bg-red-100 text-red-800 border border-red-200',
                                        'batal' => 'bg-slate-100 text-slate-700 border border-slate-200',
                                        default => 'bg-slate-100 text-slate-800',
                                    };
                                @endphp
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $cls }}">
                                    @if ($p->status === 'lunas')
                                        Lunas
                                    @elseif ($p->status === 'menunggu')
                                        Menunggu Verifikasi
                                    @elseif ($p->status === 'ditolak')
                                        Ditolak
                                    @elseif ($p->status === 'batal')
                                        Batal
                                    @else
                                        {{ ucfirst($p->status) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex justify-center gap-2 whitespace-nowrap">
                                    @if ($p->status === 'menunggu' && $p->metode_pembayaran !== 'midtrans')
                                        <button type="button" onclick="document.getElementById('acc-{{ $p->id }}').showModal()" class="inline-flex items-center gap-1 rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-200 active:scale-95">Setujui</button>

                                        <dialog id="acc-{{ $p->id }}" class="w-full max-w-sm rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden text-left whitespace-normal">
                                            <div class="p-6 text-center">
                                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mb-4">
                                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-slate-900 mb-2">Verifikasi Pembayaran</h3>
                                                <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin memverifikasi pembayaran ini?</p>
                                                <div class="flex items-center justify-center gap-3">
                                                    <button type="button" onclick="document.getElementById('acc-{{ $p->id }}').close()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition active:scale-95">
                                                        Batal
                                                    </button>
                                                    <form action="{{ route('pembayaran.acc', $p->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 shadow-sm transition active:scale-95">
                                                            Ya, Lanjutkan
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </dialog>

                                        <button type="button" onclick="document.getElementById('tolak-{{ $p->id }}').showModal()" class="inline-flex items-center gap-1 rounded-lg bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700 transition hover:bg-rose-200 active:scale-95">Tolak</button>

                                        <dialog id="tolak-{{ $p->id }}" class="w-full max-w-md rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden text-left whitespace-normal">
                                            <form method="POST" action="{{ route('pembayaran.tolak', $p->id) }}">
                                                @csrf
                                                <div class="p-6 text-center">
                                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 text-rose-600 mb-4">
                                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </div>
                                                    <h3 class="text-lg font-bold text-slate-900 mb-2">Tolak Pembayaran</h3>
                                                    <p class="text-sm text-slate-600 mb-4">Apakah Anda yakin ingin menolak pembayaran ini?</p>
                                                    
                                                    <div class="text-left mb-6">
                                                        <label for="komentar-{{ $p->id }}" class="block text-xs font-semibold text-slate-700 mb-1">Alasan Penolakan</label>
                                                        <textarea id="komentar-{{ $p->id }}" name="komentar" rows="3" class="w-full rounded-xl border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500" placeholder="Masukkan alasan penolakan" required></textarea>
                                                    </div>

                                                    <div class="flex items-center justify-center gap-3">
                                                        <button type="button" onclick="document.getElementById('tolak-{{ $p->id }}').close()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition active:scale-95">
                                                            Batal
                                                        </button>
                                                        <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 shadow-sm transition active:scale-95">
                                                            Ya, Lanjutkan
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </dialog>
                                    @else
                                        <span class="text-xs text-slate-500 max-w-[140px] truncate" title="{{ $p->admin_komentar }}">{{ $p->admin_komentar ?? '—' }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-4 py-8 text-center text-slate-500">Tidak ada data pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
