<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function pay(Tagihan $tagihan)
    {
        $penghuni = Auth::user()->penghuni;
        abort_if(! $penghuni || $tagihan->penghuni_id !== $penghuni->id, 403);

        if ($tagihan->status !== 'belum_bayar') {
            return redirect()->route('penghuni.tagihan.index')->with('error', 'Tagihan ini tidak dapat dibayar (sudah lunas atau menunggu verifikasi).');
        }

        // Cek apakah ada pembayaran midtrans yang pending
        $pembayaran = Pembayaran::where('tagihan_id', $tagihan->id)
            ->where('metode_pembayaran', 'midtrans')
            ->where('status', 'menunggu')
            ->first();

        $orderId = 'PAY-' . $tagihan->id . '-' . time();

        if (!$pembayaran) {
            $pembayaran = Pembayaran::create([
                'penghuni_id' => $tagihan->penghuni_id,
                'tagihan_id' => $tagihan->id,
                'order_id' => $orderId,
                'jumlah' => $tagihan->jumlah,
                'tanggal_bayar' => now()->toDateString(),
                'status' => 'menunggu',
                'metode_pembayaran' => 'midtrans',
            ]);
        } else {
            // Update order ID untuk menghindari duplicate order ID di midtrans jika transaksi sebelumnya fail
            $pembayaran->update([
                'order_id' => $orderId,
                'tanggal_bayar' => now()->toDateString(),
            ]);
        }

        $tagihan->update(['status' => 'menunggu']);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $tagihan->jumlah,
            ],
            'customer_details' => [
                'first_name' => $penghuni->nama,
                'phone' => $penghuni->no_hp,
            ],
            'item_details' => [
                [
                    'id' => (string) $tagihan->id,
                    'price' => (int) $tagihan->jumlah,
                    'quantity' => 1,
                    'name' => 'Sewa Kos ' . $tagihan->labelPeriode()
                ]
            ]
        ];

        Log::info('Midtrans Payload: ' . json_encode($params));

        try {
            $snapToken = Snap::getSnapToken($params);
            $pembayaran->update(['snap_token' => $snapToken]);
            
            return view('penghuni.tagihan.midtrans', compact('tagihan', 'snapToken'));
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            $tagihan->update(['status' => 'belum_bayar']);
            return redirect()->route('penghuni.tagihan.index')->with('error', 'Gagal memproses pembayaran dengan Midtrans.');
        }
    }

    public function webhook(Request $request)
    {
        Log::info('Midtrans Webhook Hit: ' . json_encode($request->all()));
        try {
            $notif = new Notification();
            
            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $order_id = $notif->order_id;
            $fraud = $notif->fraud_status;

            $pembayaran = Pembayaran::where('order_id', $order_id)->first();
            
            if (!$pembayaran) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $tagihan = $pembayaran->tagihan;

            if ($transaction == 'capture') {
                if ($type == 'credit_card'){
                    if($fraud == 'challenge'){
                        $pembayaran->update(['status' => 'menunggu']);
                    }
                    else {
                        $pembayaran->update(['status' => 'lunas']);
                        $tagihan->update(['status' => 'lunas']);
                    }
                }
            } else if ($transaction == 'settlement'){
                $pembayaran->update(['status' => 'lunas']);
                $tagihan->update(['status' => 'lunas']);
            } else if($transaction == 'pending'){
                $pembayaran->update(['status' => 'menunggu']);
            } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                $pembayaran->update(['status' => 'ditolak']);
                $tagihan->update(['status' => 'belum_bayar']);
            }

            return response()->json(['message' => 'Webhook handled successfully']);
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error handling webhook'], 500);
        }
    }
}
