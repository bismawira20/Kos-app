<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-slate-800">Pembayaran Midtrans</h2>
            <p class="text-sm text-slate-500">Selesaikan pembayaran Anda</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
            <h3 class="font-medium text-slate-800">Detail Tagihan</h3>
        </div>
        <div class="p-6">
            <dl class="mb-6 divide-y divide-slate-100 text-sm">
                <div class="flex justify-between py-3">
                    <dt class="text-slate-500">Periode</dt>
                    <dd class="font-medium text-slate-900">{{ $tagihan->labelPeriode() }}</dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-slate-500">Jatuh Tempo</dt>
                    <dd class="font-medium text-slate-900">{{ $tagihan->jatuh_tempo?->format('d/m/Y') }}</dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-slate-500">Total Tagihan</dt>
                    <dd class="font-semibold text-slate-900 text-lg">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</dd>
                </div>
            </dl>

            <div class="text-center">
                <button id="pay-button" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                    Bayar Sekarang
                </button>
                <p class="mt-4 text-xs text-slate-500">Anda akan diarahkan ke pop-up Midtrans untuk menyelesaikan pembayaran.</p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript"
                src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
                data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script type="text/javascript">
            document.getElementById('pay-button').onclick = function(){
                snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result){
                        window.location.href = '{{ route("penghuni.tagihan.index") }}';
                    },
                    onPending: function(result){
                        window.location.href = '{{ route("penghuni.tagihan.index") }}';
                    },
                    onError: function(result){
                        alert('Pembayaran gagal!');
                        window.location.href = '{{ route("penghuni.tagihan.index") }}';
                    },
                    onClose: function(){
                        alert('Anda menutup pop-up sebelum menyelesaikan pembayaran');
                    }
                });
            };
        </script>
    @endpush
</x-app-layout>
