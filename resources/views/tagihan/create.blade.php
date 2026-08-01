<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">Tagihan manual</h2>
    </x-slot>

    <div class="max-w-lg rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <form method="POST" action="{{ route('tagihan.store') }}" class="space-y-4">
            @csrf
            <div>
                <x-input-label value="Penghuni" />
                <select name="penghuni_id" class="mt-1 block w-full rounded-lg border-slate-300 text-sm" required>
                    @foreach ($penghuni as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }} — Kamar {{ $p->kamar?->nomor_kamar }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('penghuni_id')" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Tahun" />
                    <x-text-input name="tahun" type="number" class="mt-1 w-full" :value="old('tahun', now()->year)" required />
                    <x-input-error :messages="$errors->get('tahun')" />
                </div>
                <div>
                    <x-input-label value="Bulan" />
                    <select name="bulan" class="mt-1 block w-full rounded-lg border-slate-300 text-sm" required>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @selected((int) old('bulan', now()->month) === $i)>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="rounded-lg bg-slate-50 p-3 text-xs text-slate-600">
                <p>Harga tagihan akan otomatis mengikuti harga kamar yang ditempati penghuni.</p>
            </div>
            <div>
                <x-input-label value="Jatuh tempo" />
                <x-text-input name="jatuh_tempo" type="date" class="mt-1 w-full" :value="old('jatuh_tempo', now()->addDays(10)->format('Y-m-d'))" required />
            </div>
            <div>
                <x-input-label value="Batas toleransi" />
                <x-text-input name="batas_toleransi" type="date" class="mt-1 w-full" :value="old('batas_toleransi', now()->addDays(31)->format('Y-m-d'))" required />
                <x-input-error :messages="$errors->get('batas_toleransi')" />
            </div>
            <div class="flex gap-3">
                <x-primary-button>Simpan</x-primary-button>
                <a href="{{ route('tagihan.index') }}" class="text-sm text-slate-600 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
