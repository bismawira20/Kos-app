<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">Tambah transaksi operasional</h2>
    </x-slot>

    <div class="mx-auto max-w-2xl rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <form method="POST" action="{{ route('transaksi-operasional.store') }}" class="grid gap-4 md:grid-cols-2">
            @csrf
            <div>
                <x-input-label value="Tanggal" />
                <x-text-input name="tanggal" type="date" class="mt-1 block w-full" :value="old('tanggal', now()->toDateString())" required />
                <x-input-error :messages="$errors->get('tanggal')" />
            </div>
            <div>
                <x-input-label value="Jenis" />
                <select name="jenis" class="mt-1 block w-full rounded-lg border-slate-300 text-sm" required>
                    <option value="pemasukan" @selected(old('jenis') === 'pemasukan')>Pemasukan</option>
                    <option value="pengeluaran" @selected(old('jenis') === 'pengeluaran')>Pengeluaran</option>
                </select>
                <x-input-error :messages="$errors->get('jenis')" />
            </div>
            <div>
                <x-input-label value="Kategori" />
                <x-text-input name="kategori" type="text" class="mt-1 block w-full" :value="old('kategori')" required placeholder="Operasional, perbaikan, listrik" />
                <x-input-error :messages="$errors->get('kategori')" />
            </div>
            <div>
                <x-input-label value="Jumlah" />
                <x-text-input name="jumlah" type="number" class="mt-1 block w-full" :value="old('jumlah')" required />
                <x-input-error :messages="$errors->get('jumlah')" />
            </div>
            <div class="md:col-span-2">
                <x-input-label value="Deskripsi" />
                <x-text-input name="deskripsi" type="text" class="mt-1 block w-full" :value="old('deskripsi')" required placeholder="Contoh: Ganti lampu lorong lantai 2" />
                <x-input-error :messages="$errors->get('deskripsi')" />
            </div>
            <div class="md:col-span-2">
                <x-input-label value="Sumber (opsional)" />
                <x-text-input name="sumber" type="text" class="mt-1 block w-full" :value="old('sumber')" placeholder="Kas kos, transfer, dll." />
                <x-input-error :messages="$errors->get('sumber')" />
            </div>
            <div class="md:col-span-2 flex items-center justify-end gap-3">
                <a href="{{ route('transaksi-operasional.index') }}" class="text-sm text-slate-600 hover:underline">Batal</a>
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
