<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">Ubah laporan kendala</h2>
    </x-slot>

    <div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
        <p class="text-sm text-slate-600">Perbarui deskripsi sebelum laporan diproses admin.</p>

        <form method="POST" action="{{ route('penghuni.kendala.update', $kendala) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <x-input-label value="Alasan / deskripsi kendala" />
                <textarea name="deskripsi" rows="5" class="mt-1 block w-full rounded-lg border-slate-300 text-sm" required>{{ old('deskripsi', $kendala->deskripsi) }}</textarea>
                <x-input-error :messages="$errors->get('deskripsi')" />
            </div>
            <div>
                <x-input-label value="Foto bukti (opsional)" />
                <input type="file" name="bukti" accept="image/*" class="mt-1 block w-full text-sm">
                <x-input-error :messages="$errors->get('bukti')" />
            </div>
            <button type="submit" class="w-full rounded-lg bg-violet-600 py-3 text-sm font-semibold text-white hover:bg-violet-700">Simpan perubahan</button>
            <a href="{{ route('penghuni.kendala.index') }}" class="block text-center text-sm text-slate-600">Batal</a>
        </form>
    </div>
</x-app-layout>