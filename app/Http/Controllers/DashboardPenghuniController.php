<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardPenghuniController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $user->load('penghuni.kamar');
        $penghuni = $user->penghuni;

        if (! $penghuni) {
            return view('dashboard_penghuni', [
                'penghuni' => null,
                'tagihanTerbaru' => null,
                'stats' => [],
            ]);
        }

        $now = now();
        $tagihanBulanIni = Tagihan::where('penghuni_id', $penghuni->id)
            ->where('tahun', $now->year)
            ->where('bulan', $now->month)
            ->first();

        $tagihanTerbaru = Tagihan::where('penghuni_id', $penghuni->id)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();

        $tunggakan = Tagihan::where('penghuni_id', $penghuni->id)
            ->whereIn('status', ['belum_bayar', 'menunggu'])
            ->where('jatuh_tempo', '<', $now->toDateString())
            ->count();

        $hariKeJatuhTempo = null;
        if ($tagihanBulanIni && $tagihanBulanIni->jatuh_tempo) {
            $due = $tagihanBulanIni->jatuh_tempo->copy()->startOfDay();
            $hariKeJatuhTempo = now()->startOfDay()->diffInDays($due, false);
        }

        $stats = [
            'tagihan_bulan_ini' => $tagihanBulanIni,
            'tunggakan' => $tunggakan,
            'hari_jatuh_tempo' => $hariKeJatuhTempo,
        ];

        return view('dashboard_penghuni', compact('penghuni', 'tagihanTerbaru', 'stats'));
    }
}
