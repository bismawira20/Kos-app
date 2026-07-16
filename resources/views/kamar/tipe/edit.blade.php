<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-slate-900">Ubah Harga Tipe Kamar</h2>
        <p class="mt-1 text-sm text-slate-600">Sesuaikan nama tipe kamar dan tarif bulanan standar</p>
    </x-slot>

    <div class="mx-auto max-w-lg py-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('tipe-kamar.update', $tipeKamar) }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-slate-700">Tipe Kamar</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $tipeKamar->nama) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                        @error('nama')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="harga" class="block text-sm font-medium text-slate-700">Harga Standar / Bulan</label>
                        <div class="relative mt-1 rounded-lg shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-slate-500 text-sm">Rp</span>
                            </div>
                            <input type="text" name="harga" id="harga" value="{{ old('harga', number_format($tipeKamar->harga, 0, ',', '.')) }}" class="block w-full rounded-lg border-slate-300 pl-10 pr-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required oninput="formatRupiah(this)">
                        </div>
                        @error('harga')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('tipe-kamar.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition active:scale-95">Batal</a>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition active:scale-95">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function formatRupiah(input) {
                let value = input.value.replace(/[^,\d]/g, '').toString();
                let split = value.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                input.value = rupiah;
            }
        </script>
    @endpush
</x-app-layout>
