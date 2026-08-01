<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Kendala / laporan</h2>
                <p class="text-sm text-slate-500">Catatan operasional, prioritas masalah, dan tindak lanjut</p>
            </div>
            @if ($penghuni)
                <a href="{{ route('penghuni.kendala.create') }}" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white">+ Laporkan kendala</a>
            @endif
        </div>
    </x-slot>

    @if (! $penghuni)
        <p class="text-slate-500">Akun belum terhubung.</p>
    @else
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Tanggal</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Deskripsi</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Catatan</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($laporan as $l)
                        <tr>
                            <td class="px-4 py-3 text-center whitespace-nowrap text-slate-600">{{ $l->created_at?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center max-w-md text-slate-900 font-medium">{{ \Illuminate\Support\Str::limit($l->deskripsi, 80) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold border
                                    {{ $l->status === 'selesai' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($l->status === 'ditolak' ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-amber-50 text-amber-800 border-amber-200') }}">
                                    {{ strtoupper(str_replace('_', ' ', $l->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-slate-600">{{ $l->alasan_tolak ?? $l->catatan_admin ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2 whitespace-nowrap">
                                    @if ($l->status === 'menunggu')
                                        <a href="{{ route('penghuni.kendala.edit', $l) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-200 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>
                                        <button type="button" onclick="document.getElementById('delete-kendala-{{ $l->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-100 px-3 py-1.5 text-sm font-medium text-rose-700 transition hover:bg-rose-200 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>

                                        {{-- Delete Modal --}}
                                        <dialog id="delete-kendala-{{ $l->id }}" class="w-full max-w-sm rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden text-left">
                                            <div class="p-8 text-center">
                                                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-4 border-amber-400 text-amber-400 mb-6">
                                                    <span class="text-5xl font-light leading-none -mt-1">!</span>
                                                </div>
                                                <h3 class="text-2xl font-bold text-slate-800 tracking-wider uppercase mb-2">DELETE</h3>
                                                <p class="text-slate-600 mb-8">Hapus Laporan Kendala : <span class="font-semibold">{{ \Illuminate\Support\Str::limit($l->deskripsi, 30) }}</span> ?</p>
                                                <div class="flex items-center justify-center gap-4">
                                                    <form action="{{ route('penghuni.kendala.destroy', $l) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded-lg bg-rose-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-rose-700 shadow-md transition active:scale-95">
                                                            Yes, delete!
                                                        </button>
                                                    </form>
                                                    <button type="button" onclick="document.getElementById('delete-kendala-{{ $l->id }}').close()" class="rounded-lg bg-slate-400 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-500 shadow-md transition active:scale-95">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </dialog>
                                    @elseif ($l->status === 'diperbaiki')
                                        {{-- Penghuni menentukan apakah sudah selesai atau belum selesai --}}
                                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-100 px-3 py-1.5 text-sm font-medium text-amber-800 transition hover:bg-amber-200 active:scale-95" onclick="document.getElementById('belum-selesai-{{ $l->id }}').showModal()">
                                            Belum selesai
                                        </button>

                                        <form action="{{ route('penghuni.kendala.konfirmasi', $l->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-emerald-700 active:scale-95">
                                                Selesai
                                            </button>
                                        </form>

                                        <dialog id="belum-selesai-{{ $l->id }}" class="w-full max-w-md rounded-xl p-0 shadow-2xl backdrop:bg-slate-900/40 text-left">
                                            <form method="POST" action="{{ route('penghuni.kendala.lapor-ulang', $l->id) }}" class="p-6">
                                                @csrf
                                                <h3 class="text-lg font-semibold text-slate-900">Berikan tanggapan</h3>
                                                <p class="mt-1 text-sm text-slate-500">Tulis alasan/keluhan karena perbaikan belum sempurna.</p>
                                                <textarea name="feedback_penghuni" rows="4" required class="mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: masih ada kebocoran / suara masih bising / belum maksimal ...">{{ old('feedback_penghuni') }}</textarea>
                                                <div class="mt-4 flex justify-end gap-2">
                                                    <button type="button" onclick="document.getElementById('belum-selesai-{{ $l->id }}').close()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Batal</button>
                                                    <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">Kirim tanggapan</button>
                                                </div>
                                            </form>
                                        </dialog>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Terkunci</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada laporan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</x-app-layout>
