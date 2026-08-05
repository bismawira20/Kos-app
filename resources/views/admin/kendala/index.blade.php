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
                    <option value="menunggu" @selected(($filterStatus ?? '') === 'menunggu')>Menunggu</option>
                    <option value="proses" @selected(($filterStatus ?? '') === 'proses')>Sedang Dikerjakan</option>
                    <option value="diperbaiki" @selected(($filterStatus ?? '') === 'diperbaiki')>Sudah Diperbaiki</option>
                    <option value="selesai" @selected(($filterStatus ?? '') === 'selesai')>Selesai</option>
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
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">No</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Nama</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">No. Telepon</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Jenis / Uraian Kendala</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Bukti</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($laporan as $k)
                        @php
                            $badge = match ($k->status) {
                                'menunggu' => ['bg-amber-50 text-amber-800 border border-amber-200', 'Menunggu'],
                                'proses' => ['bg-blue-50 text-blue-800 border border-blue-200', 'Sedang Dikerjakan'],
                                'diperbaiki' => ['bg-indigo-50 text-indigo-800 border border-indigo-200', 'Sudah Diperbaiki'],
                                'selesai' => ['bg-emerald-50 text-emerald-800 border border-emerald-200', 'Selesai'],
                                'ditolak' => ['bg-rose-50 text-rose-800 border border-rose-200', 'Ditolak'],
                                default => ['bg-slate-50 text-slate-800 border border-slate-200', $k->status],
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80 align-middle">
                            <td class="whitespace-nowrap px-3 py-3 text-center text-slate-700">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 text-center font-medium text-slate-900">{{ $k->penghuni?->nama ?? '—' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-center text-slate-700">{{ $k->penghuni?->no_hp ?? '—' }}</td>
                            <td class="max-w-md px-3 py-3 text-center text-slate-700">
                                <span class="line-clamp-2" title="{{ $k->deskripsi }}">{{ \Illuminate\Support\Str::limit($k->deskripsi, 100) }}</span>
                                @if ($k->penghuni?->kamar)
                                    <span class="mt-0.5 block text-xs text-slate-500 font-semibold">Kamar {{ $k->penghuni->kamar->nomor_kamar }}</span>
                                @endif
                                @if ($k->feedback_penghuni && $k->status !== 'selesai')
                                    <div class="mt-1 text-xs text-rose-600 bg-rose-50 border border-rose-100 rounded-lg p-2 text-left">
                                        <strong>Feedback Penghuni:</strong> {{ $k->feedback_penghuni }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex justify-center">
                                    @if ($k->bukti)
                                        <button type="button" onclick="document.getElementById('bukti-{{ $k->id }}').showModal()" class="block">
                                            <img src="{{ asset('uploads/kendala/'.$k->bukti) }}" alt="" class="h-12 w-12 rounded object-cover ring-1 ring-slate-200">
                                        </button>
                                        <dialog id="bukti-{{ $k->id }}" class="max-w-lg rounded-xl p-0">
                                            <form method="dialog" class="p-4">
                                                <img src="{{ asset('uploads/kendala/'.$k->bukti) }}" class="max-h-[70vh] w-full rounded-lg object-contain" alt="Bukti">
                                                <button class="mt-3 w-full rounded-lg bg-slate-200 py-2 text-sm">Tutup</button>
                                            </form>
                                        </dialog>
                                    @else
                                        —
                                    @endif
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge[0] }}">{{ $badge[1] }}</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if ($k->status === 'menunggu')
                                        <form action="{{ route('kendala.kerjakan', $k->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-1.5 text-sm font-medium text-blue-700 transition hover:bg-blue-200 active:scale-95">
                                                Kerjakan
                                            </button>
                                        </form>

                                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-100 px-3 py-1.5 text-sm font-medium text-rose-700 transition hover:bg-rose-200 active:scale-95" onclick="document.getElementById('tolak-{{ $k->id }}').showModal()">
                                            Tolak
                                        </button>
                                    @elseif ($k->status === 'proses')
                                        <form action="{{ route('kendala.diperbaiki', $k->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-100 px-3 py-1.5 text-sm font-medium text-indigo-700 transition hover:bg-indigo-200 active:scale-95">
                                                Selesai Perbaikan
                                            </button>
                                        </form>

                                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-100 px-3 py-1.5 text-sm font-medium text-rose-700 transition hover:bg-rose-200 active:scale-95" onclick="document.getElementById('tolak-{{ $k->id }}').showModal()">
                                            Tolak
                                        </button>
                                    @elseif ($k->status === 'diperbaiki')
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                            Menunggu Konfirmasi Penghuni
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif

                                    {{-- Modals --}}
                                    <dialog id="tolak-{{ $k->id }}" class="w-full max-w-md rounded-xl p-0 shadow-2xl backdrop:bg-slate-900/40 text-left">
                                        <form method="POST" action="{{ route('kendala.tolak', $k->id) }}" class="p-6">
                                            @csrf
                                            <h3 class="text-lg font-semibold text-slate-900">Tolak laporan</h3>
                                            <p class="mt-1 text-sm text-slate-500">Berikan alasan penolakan kepada penghuni.</p>
                                            <textarea name="alasan_tolak" rows="4" class="mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="Alasan penolakan"></textarea>
                                            <div class="mt-4 flex justify-end gap-2">
                                                <button type="button" onclick="document.getElementById('tolak-{{ $k->id }}').close()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Batal</button>
                                                <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Kirim</button>
                                            </div>
                                        </form>
                                    </dialog>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">Belum ada data laporan kendala.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer aksi seperti referensi --}}
        <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 bg-white px-4 py-4 sm:px-6">
            <a href="{{ route('kendala.export', request()->query()) }}" class="inline-flex items-center rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                Export Excel
            </a>
            <span class="text-xs text-slate-500">File CSV dapat dibuka di Microsoft Excel.</span>
        </div>
    </div>
</x-app-layout>
