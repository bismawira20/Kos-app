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
                                    <td class="px-4 py-3 text-right">
                                        <form action="{{ route('penghuni.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Hapus penghuni ini? Pembayaran terkait ikut terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
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
