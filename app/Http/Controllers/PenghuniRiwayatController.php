<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PenghuniRiwayatController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $penghuni = $user->penghuni;

        if (! $penghuni) {
            return view('penghuni.riwayat', ['penghuni' => null, 'pembayaran' => collect()]);
        }

        $pembayaran = Pembayaran::with(['tagihan.kamar'])
            ->where('penghuni_id', $penghuni->id)
            ->where('status', '!=', 'batal')
            ->where(function($query) {
                $query->where('status', '!=', 'menunggu')
                      ->orWhere(function($sub) {
                          $sub->where('status', 'menunggu')
                              ->where(function($sub2) {
                                  $sub2->whereNull('metode_pembayaran')
                                       ->orWhere('metode_pembayaran', '!=', 'midtrans');
                              });
                      });
            })
            ->orderByDesc('created_at')
            ->get();

        return view('penghuni.riwayat', compact('penghuni', 'pembayaran'));
    }
}
