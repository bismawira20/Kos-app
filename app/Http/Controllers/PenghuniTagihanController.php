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
    public function index(): View
    {
        $user = Auth::user();
        $penghuni = $user->penghuni;
        if (! $penghuni) {
            return view('penghuni.tagihan.index', ['penghuni' => null, 'tagihans' => collect()]);
        }

        $tagihans = Tagihan::with('kamar')
            ->where('penghuni_id', $penghuni->id)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        return view('penghuni.tagihan.index', compact('penghuni', 'tagihans'));
    }

    public function bayar(Tagihan $tagihan): View
    {
        $this->authorizeTagihan($tagihan);
        $tagihan->load('kamar', 'penghuni');

        return view('penghuni.tagihan.bayar', compact('tagihan'));
    }

    public function kirim(Request $request, Tagihan $tagihan): RedirectResponse
    {
        $this->authorizeTagihan($tagihan);

        if ($tagihan->status !== 'belum_bayar') {
            return redirect()->route('penghuni.tagihan.index')->with('error', 'Tagihan ini tidak dapat dibayar (sudah lunas atau menunggu verifikasi).');
        }

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

    private function authorizeTagihan(Tagihan $tagihan): void
    {
        $penghuni = Auth::user()->penghuni;
        abort_if(! $penghuni || $tagihan->penghuni_id !== $penghuni->id, 403);
    }
}
