<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return response()->json([
                'role' => 'admin',
                'total_tagihan_lunas' => Tagihan::where('status', 'lunas')->count(),
                'tagihan_menunggu' => Tagihan::where('status', 'menunggu')->count(),
                'pembayaran_menunggu' => Pembayaran::where('status', 'menunggu')->count(),
            ]);
        }

        $penghuni = $user->penghuni;
        abort_if(! $penghuni, 404);

        $tagihan = Tagihan::where('penghuni_id', $penghuni->id)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->limit(5)
            ->get(['id', 'tahun', 'bulan', 'jumlah', 'status']);

        return response()->json([
            'role' => 'penghuni',
            'name' => $penghuni->nama,
            'room' => $penghuni->kamar?->nomor_kamar,
            'recent_tagihan' => $tagihan,
        ]);
    }
}
