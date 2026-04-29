<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data kamar</h2>
            <a href="{{ route('kamar.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700">
                Tambah kamar
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
                                <th class="px-4 py-3 text-left font-medium text-gray-600">No</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Nomor</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Harga / bulan</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($kamar as $k)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 text-gray-700">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $k->nomor_kamar }}</td>
                                    <td class="px-4 py-3 text-gray-700">Rp {{ number_format($k->harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                                            {{ $k->status === 'kosong' ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                            {{ $k->status === 'kosong' ? 'Kosong' : 'Terisi' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('kamar.edit', $k) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form action="{{ route('kamar.destroy', $k) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kamar ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data kamar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
