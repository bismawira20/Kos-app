<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">Bayar via QRIS (Simulasi)</h2>
    </x-slot>

    <div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
        <h3 class="text-lg font-semibold text-slate-900">Scan QRIS untuk membayar</h3>

        <dl class="mt-4 space-y-2 rounded-lg bg-slate-50 p-4 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Periode</dt><dd class="font-medium">{{ $tagihan->labelPeriode() }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Jumlah tagihan</dt><dd>Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</dd></div>
        </dl>

        <div class="mt-6 flex justify-center">
            <img src="{{ $qrUrl }}" alt="QRIS" class="w-64 h-64 rounded-md bg-white shadow" />
        </div>

        <p class="mt-4 text-sm text-slate-600">Gunakan aplikasi pembayaran Anda untuk memindai QR. Ini adalah halaman simulasi — sistem tidak terhubung ke gateway nyata.</p>

        <form method="POST" action="{{ route('penghuni.tagihan.qris.confirm', $tagihan) }}" class="mt-6">
            @csrf
            <button type="submit" class="w-full rounded-lg bg-indigo-600 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Simulasikan pembayaran (buat record)</button>
        </form>

        <a href="{{ route('penghuni.tagihan.index') }}" class="mt-3 block text-center text-sm text-slate-600 hover:underline">Kembali ke tagihan</a>
    </div>
</x-app-layout>
