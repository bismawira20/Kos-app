<x-app-layout>
    {{-- Judul utama di area konten (seperti Dashboard Pengaduan) --}}

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-4 py-4 sm:px-6">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Kendala</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola laporan kerusakan dan keluhan penghuni kos</p>
        </div>

        {{-- Filter: dropdown + tombol Filter (bukan auto-submit) --}}
        <form method="GET" action="{{ route('kendala.index') }}" class="flex flex-wrap items-end gap-3 border-b border-slate-100 bg-slate-50 px-4 py-4 sm:px-6">
            <div>
                <label for="status" class="mb-1 block text-xs font-medium text-slate-600">Status</label>
                <select id="status" name="status" class="min-w-[180px] rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="menunggu" @selected(($filterStatus ?? '') === 'menunggu')>Diproses</option>
                    <option value="selesai" @selected(($filterStatus ?? '') === 'selesai')>Disetujui</option>
                    <option value="ditolak" @selected(($filterStatus ?? '') === 'ditolak')>Ditolak</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                Filter
            </button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">No</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Nama</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">No. Telepon</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Jenis / Uraian Kendala</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($laporan as $k)
                        @php
                            $badge = match ($k->status) {
                                'menunggu' => ['bg-amber-100 text-amber-900 ring-amber-200', 'Diproses'],
                                'selesai' => ['bg-emerald-100 text-emerald-800 ring-emerald-200', 'Disetujui'],
                                'ditolak' => ['bg-red-100 text-red-800 ring-red-200', 'Ditolak'],
                                default => ['bg-slate-100 text-slate-800 ring-slate-200', $k->status],
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-3 py-3 text-slate-700">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $k->penghuni?->nama ?? '—' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-slate-700">{{ $k->penghuni?->no_hp ?? '—' }}</td>
                            <td class="max-w-md px-3 py-3 text-slate-700">
                                <span class="line-clamp-2" title="{{ $k->deskripsi }}">{{ \Illuminate\Support\Str::limit($k->deskripsi, 100) }}</span>
                                @if ($k->penghuni?->kamar)
                                    <span class="mt-0.5 block text-xs text-slate-500">Kamar {{ $k->penghuni->kamar->nomor_kamar }}</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $badge[0] }}">{{ $badge[1] }}</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3">
                                <div class="flex flex-wrap items-center justify-center gap-1.5">
                                    {{-- Kuning: detail --}}
                                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-amber-100 text-amber-800 ring-1 ring-amber-200 hover:bg-amber-200" title="Detail" onclick="document.getElementById('detail-{{ $k->id }}').showModal()">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    {{-- Biru: lihat foto / bukti --}}
                                    @if ($k->bukti)
                                        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-sky-100 text-sky-800 ring-1 ring-sky-200 hover:bg-sky-200" title="Lihat bukti" onclick="document.getElementById('foto-{{ $k->id }}').showModal()">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    @else
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-slate-100 text-slate-300 ring-1 ring-slate-200" title="Tanpa foto">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </span>
                                    @endif

                                    @if ($k->status === 'menunggu')
                                        <form action="{{ route('kendala.setujui', $k->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200 hover:bg-emerald-200" title="Setujui (sudah diperbaiki)">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-rose-100 text-rose-800 ring-1 ring-rose-200 hover:bg-rose-200" title="Tolak" onclick="document.getElementById('tolak-{{ $k->id }}').showModal()">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    @endif
                                </div>

                                <dialog id="detail-{{ $k->id }}" class="w-full max-w-lg rounded-xl p-0 shadow-2xl backdrop:bg-slate-900/40">
                                    <div class="border-b border-slate-200 px-5 py-4">
                                        <h3 class="text-lg font-semibold text-slate-900">Detail kendala</h3>
                                        <p class="text-xs text-slate-500">{{ $k->created_at?->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="max-h-[70vh] space-y-3 overflow-y-auto px-5 py-4 text-sm">
                                        <p><span class="font-medium text-slate-600">Nama:</span> {{ $k->penghuni?->nama }}</p>
                                        <p><span class="font-medium text-slate-600">Telepon:</span> {{ $k->penghuni?->no_hp }}</p>
                                        <p><span class="font-medium text-slate-600">Kamar:</span> {{ $k->penghuni?->kamar?->nomor_kamar ?? '—' }}</p>
                                        <div>
                                            <span class="font-medium text-slate-600">Uraian:</span>
                                            <p class="mt-1 whitespace-pre-wrap text-slate-800">{{ $k->deskripsi }}</p>
                                        </div>
                                        @if ($k->alasan_tolak)
                                            <p class="rounded-md bg-red-50 p-2 text-red-900"><span class="font-medium">Alasan tolak:</span> {{ $k->alasan_tolak }}</p>
                                        @endif
                                        @if ($k->catatan_admin)
                                            <p class="rounded-md bg-slate-50 p-2 text-slate-800"><span class="font-medium">Catatan admin:</span> {{ $k->catatan_admin }}</p>
                                        @endif
                                    </div>
                                    <form method="dialog" class="border-t border-slate-200 px-5 py-3">
                                        <button class="w-full rounded-lg bg-slate-800 py-2 text-sm font-medium text-white hover:bg-slate-900">Tutup</button>
                                    </form>
                                </dialog>

                                @if ($k->bukti)
                                    <dialog id="foto-{{ $k->id }}" class="max-w-3xl rounded-xl p-0 shadow-2xl backdrop:bg-slate-900/40">
                                        <div class="p-4">
                                            <img src="{{ asset('kendala/'.$k->bukti) }}" alt="Bukti" class="max-h-[80vh] w-full rounded-lg object-contain">
                                        </div>
                                        <form method="dialog" class="border-t border-slate-200 px-4 py-3">
                                            <button class="w-full rounded-lg bg-slate-800 py-2 text-sm font-medium text-white">Tutup</button>
                                        </form>
                                    </dialog>
                                @endif

                                <dialog id="tolak-{{ $k->id }}" class="w-full max-w-md rounded-xl p-0 shadow-2xl backdrop:bg-slate-900/40">
                                    <form method="POST" action="{{ route('kendala.tolak', $k->id) }}" class="p-6">
                                        @csrf
                                        <h3 class="text-lg font-semibold text-slate-900">Tolak laporan</h3>
                                        <p class="mt-1 text-sm text-slate-500">Berikan alasan penolakan kepada penghuni.</p>
                                        <textarea name="alasan_tolak" rows="4" class="mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required placeholder="Alasan penolakan"></textarea>
                                        <div class="mt-4 flex justify-end gap-2">
                                            <button type="button" onclick="document.getElementById('tolak-{{ $k->id }}').close()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Batal</button>
                                            <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Kirim</button>
                                        </div>
                                    </form>
                                </dialog>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">Belum ada data laporan kendala.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer aksi seperti referensi --}}
        <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 bg-white px-4 py-4 sm:px-6">
            <form method="POST" action="{{ route('kendala.setujui-semua') }}" onsubmit="return confirm('Setujui semua laporan yang masih Diproses?');">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                    Setujui Semua
                </button>
            </form>
            <a href="{{ route('kendala.export', request()->query()) }}" class="inline-flex items-center rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                Export Excel
            </a>
            <span class="text-xs text-slate-500">File CSV dapat dibuka di Microsoft Excel.</span>
        </div>
    </div>
</x-app-layout>
