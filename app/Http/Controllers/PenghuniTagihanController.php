<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class PenghuniTagihanController extends Controller
{
    /**
     * Memeriksa apakah penghuni masih memiliki tagihan pada periode sebelumnya
     * yang belum berstatus Lunas (yaitu status: belum_bayar, menunggu, atau ditolak).
     */
    public static function hasEarlierUnpaidTagihan(Tagihan $tagihan): bool
    {
        return Tagihan::where('penghuni_id', $tagihan->penghuni_id)
            ->where('id', '!=', $tagihan->id)
            ->whereIn('status', ['belum_bayar', 'menunggu', 'ditolak'])
            ->where(function ($query) use ($tagihan) {
                $query->where('tahun', '<', $tagihan->tahun)
                    ->orWhere(function ($q) use ($tagihan) {
                        $q->where('tahun', $tagihan->tahun)
                          ->where('bulan', '<', $tagihan->bulan);
                    });
            })
            ->exists();
    }

    public function index(): View
    {
        $user = Auth::user();
        $penghuni = $user->penghuni;
        if (! $penghuni) {
            return view('penghuni.tagihan.index', ['penghuni' => null, 'tagihans' => collect()]);
        }

        $tagihans = Tagihan::with(['kamar', 'pembayaran'])
            ->where('penghuni_id', $penghuni->id)
            ->whereIn('status', ['belum_bayar', 'menunggu', 'ditolak'])
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        return view('penghuni.tagihan.index', compact('penghuni', 'tagihans'));
    }

    public function bayar(Tagihan $tagihan): View|RedirectResponse
    {
        $this->authorizeTagihan($tagihan);
        $tagihan->refresh();

        // Validasi FIFO sisi server: tolakan jika masih ada tagihan periode sebelumnya yang belum lunas
        if (self::hasEarlierUnpaidTagihan($tagihan)) {
            return redirect()->route('penghuni.tagihan.index')->with('error', 'Anda masih memiliki tagihan pada periode sebelumnya yang belum diselesaikan. Silakan selesaikan tagihan tersebut terlebih dahulu sebelum melakukan pembayaran tagihan berikutnya.');
        }

        if ($tagihan->status !== 'belum_bayar') {
            return redirect()->route('penghuni.tagihan.index')->with('error', 'Tagihan ini tidak dapat dibayar (sudah lunas atau menunggu verifikasi).');
        }

        $tagihan->load('kamar', 'penghuni');

        return view('penghuni.tagihan.bayar', compact('tagihan'));
    }

    public function kirim(Request $request, Tagihan $tagihan): RedirectResponse
    {
        $this->authorizeTagihan($tagihan);
        $tagihan->refresh();

        // Validasi FIFO sisi server saat pengiriman bukti pembayaran
        if (self::hasEarlierUnpaidTagihan($tagihan)) {
            return redirect()->route('penghuni.tagihan.index')->with('error', 'Anda masih memiliki tagihan pada periode sebelumnya yang belum diselesaikan. Silakan selesaikan tagihan tersebut terlebih dahulu sebelum melakukan pembayaran tagihan berikutnya.');
        }

        if ($tagihan->status !== 'belum_bayar') {
            return redirect()->route('penghuni.tagihan.index')->with('error', 'Tagihan ini tidak dapat dibayar (sudah lunas atau menunggu verifikasi).');
        }

        $request->merge(['jumlah' => str_replace('.', '', $request->jumlah)]);
        $validated = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
            'bukti' => ['required', 'image', 'max:5120'],
        ]);

        if (! File::isDirectory(public_path('bukti'))) {
            File::makeDirectory(public_path('bukti'), 0755, true);
        }

        $file = $request->file('bukti');
        $namaFile = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('bukti'), $namaFile);

        Pembayaran::create([
            'penghuni_id' => $tagihan->penghuni_id,
            'tagihan_id' => $tagihan->id,
            'jumlah' => $validated['jumlah'],
            'tanggal_bayar' => now()->toDateString(),
            'status' => 'menunggu',
            'bukti' => $namaFile,
        ]);

        $tagihan->update(['status' => 'menunggu']);

        return redirect()->route('penghuni.tagihan.index')->with('status', 'Bukti pembayaran dikirim. Menunggu verifikasi admin.');
    }

    public function downloadInvoice(Tagihan $tagihan)
    {
        $this->authorizeTagihan($tagihan);
        
        if ($tagihan->status !== 'lunas') {
            return redirect()->route('penghuni.tagihan.index')->with('error', 'Kuitansi hanya tersedia untuk tagihan yang sudah lunas.');
        }

        $tagihan->load(['penghuni', 'kamar']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penghuni.tagihan.invoice', compact('tagihan'));
        
        return $pdf->download('Invoice-'.$tagihan->labelPeriode().'.pdf');
    }

    private function authorizeTagihan(Tagihan $tagihan): void
    {
        $penghuni = Auth::user()->penghuni;
        abort_if(! $penghuni || $tagihan->penghuni_id !== $penghuni->id, 403);
    }
}
