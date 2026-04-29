<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Transaksi operasional</h2>
                <p class="text-sm text-slate-500">Pendapatan, pengeluaran, dan saldo operasional</p>
            </div>
            <a href="{{ route('transaksi-operasional.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">+ Tambah transaksi</a>
        </div>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-xs uppercase tracking-wide text-slate-500">Total pemasukan</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">Rp {{ number_format($pemasukan, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-xs uppercase tracking-wide text-slate-500">Total pengeluaran</p>
            <p class="mt-2 text-2xl font-bold text-rose-600">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-xs uppercase tracking-wide text-slate-500">Saldo</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">Rp {{ number_format($pemasukan - $pengeluaran, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">Jumlah</th>
                        <th class="px-4 py-3 text-left">Sumber</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transaksi as $item)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $item->tanggal?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $item->jenis === 'pemasukan' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ strtoupper($item->jenis) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $item->kategori }}</td>
                            <td class="px-4 py-3">{{ $item->deskripsi }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $item->sumber ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('transaksi-operasional.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada transaksi operasional.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
