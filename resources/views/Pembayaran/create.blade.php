<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">Catat pembayaran (admin)</h2>
    </x-slot>

    <div class="max-w-lg rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <form method="POST" action="{{ route('pembayaran.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <x-input-label value="Penghuni" />
                <select name="penghuni_id" class="mt-1 block w-full rounded-lg border-slate-300 text-sm" required>
                    @foreach ($penghuni as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Jumlah (Rp)" />
                <x-text-input name="jumlah" type="text" class="mt-1 w-full" 
                    :value="old('jumlah') ? number_format((int)str_replace('.', '', old('jumlah')), 0, ',', '.') : ''"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                    required />
            </div>
            <div>
                <x-input-label value="Tanggal bayar" />
                <x-text-input name="tanggal_bayar" type="date" class="mt-1 w-full" :value="now()->format('Y-m-d')" />
            </div>
            <div>
                <x-input-label value="Status" />
                <select name="status" class="mt-1 block w-full rounded-lg border-slate-300 text-sm">
                    <option value="lunas">Langsung lunas (tanpa verifikasi)</option>
                    <option value="menunggu">Menunggu verifikasi</option>
                </select>
            </div>
            <div>
                <x-input-label value="Bukti (opsional)" />
                <input type="file" name="bukti" accept="image/*" class="mt-1 block w-full text-sm">
            </div>
            <x-primary-button>Simpan</x-primary-button>
            <a href="{{ route('pembayaran.index') }}" class="ms-3 text-sm text-slate-600">Batal</a>
        </form>
    </div>
</x-app-layout>
