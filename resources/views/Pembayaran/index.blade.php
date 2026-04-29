<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Verifikasi pembayaran</h2>
                <p class="text-sm text-slate-500">Periksa bukti transfer penghuni</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pembayaran.index', ['filter' => 'menunggu']) }}" class="rounded-lg px-3 py-2 text-sm {{ ($filter ?? '') === 'menunggu' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-700' }}">Menunggu validasi</a>
                <a href="{{ route('pembayaran.index') }}" class="rounded-lg px-3 py-2 text-sm {{ empty($filter) ? 'bg-indigo-100 text-indigo-900' : 'bg-slate-100 text-slate-700' }}">Semua</a>
                <a href="{{ route('pembayaran.create') }}" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white">+ Catat</a>
            </div>
        </div>
    </x-slot>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-3 text-left">No</th>
                        <th class="px-3 py-3 text-left">Tanggal</th>
                        <th class="px-3 py-3 text-left">Penghuni</th>
                        <th class="px-3 py-3 text-left">Kamar</th>
                        <th class="px-3 py-3 text-left">Tagihan</th>
                        <th class="px-3 py-3 text-left">Jumlah</th>
                        <th class="px-3 py-3 text-left">Bukti</th>
                        <th class="px-3 py-3 text-left">Status</th>
                        <th class="px-3 py-3 text-left">Komentar</th>
                        <th class="px-3 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pembayaran as $p)
                        <tr class="align-top">
                            <td class="px-3 py-3">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-3">{{ $p->penghuni?->nama }}</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs text-indigo-800">{{ $p->penghuni?->kamar?->nomor_kamar ?? '—' }}</span>
                            </td>
                            <td class="px-3 py-3 text-xs">{{ $p->tagihan ? $p->tagihan->labelPeriode() : '—' }}</td>
                            <td class="px-3 py-3">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                            <td class="px-3 py-3">
                                @if ($p->bukti)
                                    <button type="button" onclick="document.getElementById('bukti-{{ $p->id }}').showModal()" class="block">
                                        <img src="{{ asset('bukti/'.$p->bukti) }}" alt="" class="h-12 w-12 rounded object-cover ring-1 ring-slate-200">
                                    </button>
                                    <dialog id="bukti-{{ $p->id }}" class="max-w-lg rounded-xl p-0">
                                        <form method="dialog" class="p-4">
                                            <img src="{{ asset('bukti/'.$p->bukti) }}" class="max-h-[70vh] w-full rounded-lg object-contain" alt="Bukti">
                                            <button class="mt-3 w-full rounded-lg bg-slate-200 py-2 text-sm">Tutup</button>
                                        </form>
                                    </dialog>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                @php
                                    $cls = match ($p->status) {
                                        'lunas' => 'bg-emerald-100 text-emerald-800',
                                        'menunggu' => 'bg-amber-100 text-amber-800',
                                        'ditolak' => 'bg-red-100 text-red-800',
                                        default => 'bg-slate-100 text-slate-800',
                                    };
                                @endphp
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $cls }}">{{ $p->status }}</span>
                            </td>
                            <td class="px-3 py-3 max-w-[140px] text-xs text-slate-600">{{ $p->admin_komentar ?? '—' }}</td>
                            <td class="px-3 py-3 text-right space-y-1">
                                @if ($p->status === 'menunggu')
                                    <form action="{{ route('pembayaran.acc', $p->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="block w-full rounded bg-emerald-600 px-2 py-1 text-xs text-white hover:bg-emerald-700">Setujui</button>
                                    </form>
                                    <button type="button" onclick="document.getElementById('tolak-{{ $p->id }}').showModal()" class="w-full rounded bg-rose-500 px-2 py-1 text-xs text-white hover:bg-rose-600">Tolak</button>
                                    <dialog id="tolak-{{ $p->id }}" class="w-full max-w-md rounded-xl p-6">
                                        <form method="POST" action="{{ route('pembayaran.tolak', $p->id) }}">
                                            @csrf
                                            <p class="font-medium text-slate-800">Tolak pembayaran</p>
                                            <p class="mt-1 text-sm text-slate-500">Berikan alasan agar penghuni dapat memperbaiki bukti.</p>
                                            <textarea name="komentar" rows="3" class="mt-3 w-full rounded-lg border-slate-300 text-sm" placeholder="Komentar / alasan" required></textarea>
                                            <div class="mt-4 flex justify-end gap-2">
                                                <button type="button" onclick="document.getElementById('tolak-{{ $p->id }}').close()" class="rounded-lg px-3 py-1.5 text-sm text-slate-600">Batal</button>
                                                <button type="submit" class="rounded-lg bg-rose-600 px-3 py-1.5 text-sm text-white">Kirim</button>
                                            </div>
                                        </form>
                                    </dialog>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-4 py-8 text-center text-slate-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
