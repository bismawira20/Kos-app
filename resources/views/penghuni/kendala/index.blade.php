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
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Catatan</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan as $l)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $l->created_at?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 max-w-md">{{ \Illuminate\Support\Str::limit($l->deskripsi, 80) }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                    {{ $l->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : ($l->status === 'ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ strtoupper(str_replace('_', ' ', $l->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $l->alasan_tolak ?? $l->catatan_admin ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($l->status === 'menunggu')
                                    <a href="{{ route('penghuni.kendala.edit', $l) }}" class="text-indigo-600 hover:underline">Ubah</a>
                                    <form action="{{ route('penghuni.kendala.destroy', $l) }}" method="POST" class="ms-3 inline" onsubmit="return confirm('Hapus laporan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Terkunci</span>
                                @endif
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
