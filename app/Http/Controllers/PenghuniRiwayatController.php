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
            ->orderByDesc('created_at')
            ->get();

        return view('penghuni.riwayat', compact('penghuni', 'pembayaran'));
    }
}
