<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Penghuni;
use App\Models\TransaksiOperasional;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $bulan = (int) ($request->bulan ?? date('n'));
        $tahun = (int) ($request->tahun ?? date('Y'));

        $totalKamar = Kamar::count();
        $kamarKosong = Kamar::where('status', 'kosong')->count();
        $totalPenghuni = Penghuni::count();
        $kamarTerisi = Kamar::where('status', 'terisi')->count();

        $menungguVerifikasi = Pembayaran::where('status', 'menunggu')->count();

        $pemasukanBulanIni = (int) Pembayaran::where('status', 'lunas')
            ->whereYear('tanggal_bayar', $tahun)
            ->whereMonth('tanggal_bayar', $bulan)
            ->sum('jumlah');

        $pemasukanOperasional = (int) TransaksiOperasional::where('jenis', 'pemasukan')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->sum('jumlah');

        $pengeluaranOperasional = (int) TransaksiOperasional::where('jenis', 'pengeluaran')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->sum('jumlah');

        $saldoOperasional = $pemasukanBulanIni + $pemasukanOperasional - $pengeluaranOperasional;

        $tagihanBelumLunasBulanIni = Tagihan::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->whereIn('status', ['belum_bayar', 'menunggu'])
            ->count();

        $occupancy = $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100) : 0;

        $chart = Pembayaran::selectRaw('DAY(created_at) as hari, COUNT(*) as total')
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->where('status', 'lunas')
            ->groupBy('hari')
            ->orderBy('hari')
            ->pluck('total', 'hari');

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('dashboard', compact(
            'totalKamar',
            'kamarKosong',
            'totalPenghuni',
            'kamarTerisi',
            'menungguVerifikasi',
            'pemasukanBulanIni',
            'pemasukanOperasional',
            'pengeluaranOperasional',
            'saldoOperasional',
            'tagihanBelumLunasBulanIni',
            'occupancy',
            'chart',
            'bulan',
            'tahun',
            'namaBulan'
        ));
    }
}
