<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Penghuni;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->get('filter');

        $q = Pembayaran::with(['penghuni.kamar', 'tagihan'])
            ->orderByDesc('created_at');

        if ($filter === 'menunggu') {
            $q->where('status', 'menunggu')
              ->where(function($query) {
                  $query->whereNull('metode_pembayaran')
                        ->orWhere('metode_pembayaran', '!=', 'midtrans');
              });
        }

        $pembayaran = $q->get();

        return view('pembayaran.index', compact('pembayaran', 'filter'));
    }

    public function create(): View
    {
        $penghuni = Penghuni::orderBy('nama')->get();

        return view('pembayaran.create', compact('penghuni'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['jumlah' => str_replace('.', '', $request->jumlah)]);
        $validated = $request->validate([
            'penghuni_id' => ['required', 'exists:penghunis,id'],
            'jumlah' => ['required', 'integer', 'min:0'],
            'tanggal_bayar' => ['nullable', 'date'],
            'status' => ['required', 'in:lunas,menunggu'],
            'bukti' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('bukti')) {
            if (! File::isDirectory(public_path('bukti'))) {
                File::makeDirectory(public_path('bukti'), 0755, true);
            }
            $file = $request->file('bukti');
            $namaFile = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('bukti'), $namaFile);
            $validated['bukti'] = $namaFile;
        }

        Pembayaran::create($validated);

        return redirect()->route('pembayaran.index')->with('status', 'Pembayaran tercatat.');
    }

    public function acc(int $id): RedirectResponse
    {
        $p = Pembayaran::with(['penghuni', 'tagihan'])->findOrFail($id);
        abort_unless($p->status === 'menunggu', 403);

        $p->status = 'lunas';
        $p->tanggal_bayar = $p->tanggal_bayar ?? now()->toDateString();
        $p->admin_komentar = null;
        $p->save();

        if ($p->tagihan) {
            $p->tagihan->update(['status' => 'lunas']);
        }

        return back()->with('status', 'Pembayaran dikonfirmasi.');
    }

    public function tolak(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'komentar' => ['required', 'string', 'max:1000'],
        ]);

        $p = Pembayaran::with(['penghuni', 'tagihan'])->findOrFail($id);
        abort_unless($p->status === 'menunggu', 403);

        $p->status = 'ditolak';
        $p->admin_komentar = $validated['komentar'];
        $p->save();

        if ($p->tagihan) {
            $p->tagihan->update(['status' => 'belum_bayar']);
        }

        return back()->with('status', 'Pembayaran ditolak.');
    }
}
