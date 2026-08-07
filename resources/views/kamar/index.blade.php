<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Data Kamar</h2>
                <p class="mt-1 text-sm text-slate-600">Kelola informasi nomor kamar, harga, dan ketersediaan kamar</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('tipe-kamar.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white border border-slate-300 px-4 py-2.5 font-semibold text-slate-700 shadow hover:bg-slate-50 transition active:scale-95">
                    <span>🏷️</span>
                    <span>Harga Kamar</span>
                </a>
                <a href="{{ route('kamar.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow hover:bg-indigo-700 transition active:scale-95">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Kamar</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl py-8">

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">No</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Nomor Kamar</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Tipe Kamar</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Harga / Bulan</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Status</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($kamar as $k)
                            <tr class="border-b border-slate-200 transition hover:bg-slate-50">
                                <td class="px-6 py-4 text-center text-slate-600">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-900">{{ $k->nomor_kamar }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex rounded-lg bg-slate-50 border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $k->tipeKamar?->nama ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-medium text-slate-700">Rp {{ number_format($k->harga, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ $k->status === 'kosong' ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                        {{ $k->status === 'kosong' ? 'Kosong' : 'Terisi' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2 whitespace-nowrap">
                                        <a href="{{ route('kamar.edit', $k) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-200 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>
                                        <button type="button" onclick="document.getElementById('delete-kamar-{{ $k->id }}').showModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-100 px-3 py-1.5 text-sm font-medium text-rose-700 transition hover:bg-rose-200 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>

                                        {{-- Delete Modal --}}
                                        <dialog id="delete-kamar-{{ $k->id }}" class="w-full max-w-sm rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50 border border-slate-100 overflow-hidden">
                                            <div class="p-6 text-center">
                                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 text-rose-600 mb-4">
                                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus Data Kamar</h3>
                                                <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin menghapus data kamar ini?</p>
                                                <div class="flex items-center justify-center gap-3">
                                                    <button type="button" onclick="document.getElementById('delete-kamar-{{ $k->id }}').close()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition active:scale-95">
                                                        Batal
                                                    </button>
                                                    <form action="{{ route('kamar.destroy', $k) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 shadow-sm transition active:scale-95">
                                                            Ya, Lanjutkan
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </dialog>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada data kamar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
