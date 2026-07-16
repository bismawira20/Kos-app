<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Daftar Harga & Tipe Kamar</h2>
                <p class="mt-1 text-sm text-slate-600">Atur harga sewa bulanan standar berdasarkan tipe kamar (AC / Non-AC)</p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl py-8">
        @if (session('status'))
            <div class="mb-6 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800 border border-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">No.</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-700">Tipe Kamar</th>
                            <th class="px-6 py-4 text-right font-semibold text-slate-700">Harga Standar / Bulan</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($tipeKamar as $tk)
                            <tr class="border-b border-slate-200 transition hover:bg-slate-50">
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                        {{ $tk->nama }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-emerald-700">
                                    Rp {{ number_format($tk->harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('tipe-kamar.edit', $tk) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3.5 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100 active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Ubah Harga</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">Belum ada tipe kamar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
