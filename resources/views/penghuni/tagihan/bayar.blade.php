<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">Upload bukti pembayaran</h2>
    </x-slot>

    <div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
        <h3 class="text-lg font-semibold text-slate-900">Pembayaran tagihan</h3>
        <dl class="mt-4 space-y-2 rounded-lg bg-slate-50 p-4 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Periode</dt><dd class="font-medium">{{ $tagihan->labelPeriode() }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Jumlah tagihan</dt><dd>Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Jatuh tempo</dt><dd>{{ $tagihan->jatuh_tempo?->format('d M Y') }}</dd></div>
        </dl>

        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-950">
            Transfer sesuai nominal ke rekening yang ditetapkan pengelola. Pastikan bukti jelas.
        </div>

        <form method="POST" action="{{ route('penghuni.tagihan.kirim', $tagihan) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            <div>
                <x-input-label value="Jumlah dibayar (Rp)" />
                <x-text-input name="jumlah" type="number" class="mt-1 w-full" :value="old('jumlah', $tagihan->jumlah)" required />
                <x-input-error :messages="$errors->get('jumlah')" />
            </div>
            <div>
                <x-input-label value="Bukti transfer" />
                <input type="file" name="bukti" accept="image/*" required class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-violet-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-violet-700">
                <x-input-error :messages="$errors->get('bukti')" />
            </div>
            <button type="submit" class="w-full rounded-lg bg-emerald-600 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                Kirim pembayaran
            </button>
            <a href="{{ route('penghuni.tagihan.qris', $tagihan) }}" class="mt-2 block w-full rounded-lg border border-slate-200 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Bayar via QRIS (Scan)</a>
            <a href="{{ route('penghuni.tagihan.index') }}" class="block text-center text-sm text-slate-600 hover:underline">Batal</a>
        </form>
    </div>
</x-app-layout>
