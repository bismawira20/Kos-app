<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data penghuni</h2>
            <a href="{{ route('penghuni.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700">
                Tambah penghuni
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Nama</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">No. HP</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Kamar</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Akun login</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($penghuni as $p)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $p->nama }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $p->no_hp }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $p->kamar?->nomor_kamar ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        @if ($p->user_id && $p->user)
                                            {{ $p->user->email }}
                                        @else
                                            <span class="text-gray-400">Belum dihubungkan</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <button type="button" class="text-indigo-600 hover:text-indigo-900 font-medium mr-3" onclick="document.getElementById('detail-penghuni-{{ $p->id }}').showModal()">
                                            Detail
                                        </button>

                                        <form action="{{ route('penghuni.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Hapus penghuni ini? Pembayaran terkait ikut terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                        </form>

                                        {{-- Modal Detail --}}
                                        <dialog id="detail-penghuni-{{ $p->id }}" class="w-full max-w-md rounded-xl p-0 shadow-2xl backdrop:bg-slate-900/40 text-left">
                                            <div class="border-b border-gray-100 px-5 py-4 bg-gray-50">
                                                <h3 class="text-lg font-bold text-gray-900">Profil Penghuni</h3>
                                            </div>
                                            <div class="px-5 py-5 space-y-4 text-sm">
                                                <div>
                                                    <h4 class="text-xs font-bold tracking-wider uppercase text-gray-400 mb-2">Data Utama</h4>
                                                    <p class="text-gray-800"><span class="font-medium text-gray-500 inline-block w-24">Nama:</span> {{ $p->nama }}</p>
                                                    <p class="text-gray-800"><span class="font-medium text-gray-500 inline-block w-24">No. HP:</span> {{ $p->no_hp }}</p>
                                                    <p class="text-gray-800"><span class="font-medium text-gray-500 inline-block w-24">Kamar:</span> {{ $p->kamar?->nomor_kamar ?? '—' }}</p>
                                                </div>
                                                
                                                <hr class="border-gray-100">

                                                <div>
                                                    <h4 class="text-xs font-bold tracking-wider uppercase text-gray-400 mb-2">Data Wali</h4>
                                                    <p class="text-gray-800"><span class="font-medium text-gray-500 inline-block w-24">Nama Wali:</span> {{ $p->nama_wali ?? '—' }}</p>
                                                    <p class="text-gray-800"><span class="font-medium text-gray-500 inline-block w-24">No. HP Wali:</span> {{ $p->no_hp_wali ?? '—' }}</p>
                                                    <div class="mt-1">
                                                        <span class="font-medium text-gray-500 inline-block w-24">Alamat:</span>
                                                        <div class="mt-1 bg-gray-50 p-2 rounded text-gray-700 text-xs border border-gray-100 whitespace-pre-wrap">
                                                            {{ $p->alamat_wali ?: 'Belum diisi' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <form method="dialog" class="border-t border-gray-100 px-5 py-3 bg-gray-50 flex justify-end">
                                                <button class="rounded-lg bg-white border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">Tutup</button>
                                            </form>
                                        </dialog>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada penghuni.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
