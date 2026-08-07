<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\KendalaLaporan;
use App\Models\Pembayaran;
use App\Models\Penghuni;
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

        // 1. Laporan Kendala Baru (hanya status 'menunggu')
        $jumlahKendalaBaru = KendalaLaporan::where('status', 'menunggu')->count();

        // 2. Pembayaran menunggu verifikasi
        $menungguVerifikasi = Pembayaran::where('status', 'menunggu')->count();

        // 3. Pemasukan bulan ini
        $pemasukanBulanIni = (int) Pembayaran::where('status', 'lunas')
            ->whereYear('tanggal_bayar', $tahun)
            ->whereMonth('tanggal_bayar', $bulan)
            ->sum('jumlah');

        // 4. Tagihan Belum Lunas (Seluruh tagihan aktif yang belum lunas)
        $tagihanBelumLunas = Tagihan::whereIn('status', ['belum_bayar', 'menunggu', 'ditolak'])->count();

        // 5. Menunggu Generate untuk periode bulan/tahun ini
        $jumlahMenungguGenerate = TagihanController::getPendingGenerationForPeriod($bulan, $tahun)->count();

        // 6. Tingkat Okupansi
        $occupancy = $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100) : 0;

        // Grafik Pembayaran Lunas per Hari
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
            'jumlahKendalaBaru',
            'menungguVerifikasi',
            'pemasukanBulanIni',
            'tagihanBelumLunas',
            'jumlahMenungguGenerate',
            'occupancy',
            'chart',
            'bulan',
            'tahun',
            'namaBulan'
        ));
    }
}
