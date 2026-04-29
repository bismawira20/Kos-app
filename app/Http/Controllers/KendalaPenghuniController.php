<?php

namespace App\Http\Controllers;

use App\Models\KendalaLaporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class KendalaPenghuniController extends Controller
{
    public function index(): View
    {
        $penghuni = Auth::user()->penghuni;
        if (! $penghuni) {
            return view('penghuni.kendala.index', ['penghuni' => null, 'laporan' => collect()]);
        }

        $laporan = KendalaLaporan::where('penghuni_id', $penghuni->id)->orderByDesc('created_at')->get();

        return view('penghuni.kendala.index', compact('penghuni', 'laporan'));
    }

    public function create(): View
    {
        $penghuni = Auth::user()->penghuni;
        abort_if(! $penghuni, 403);

        return view('penghuni.kendala.create', compact('penghuni'));
    }

    public function store(Request $request): RedirectResponse
    {
        $penghuni = Auth::user()->penghuni;
        abort_if(! $penghuni, 403);

        $validated = $request->validate([
            'deskripsi' => ['required', 'string', 'max:2000'],
            'bukti' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = [
            'penghuni_id' => $penghuni->id,
            'deskripsi' => $validated['deskripsi'],
            'status' => 'menunggu',
        ];

        if ($request->hasFile('bukti')) {
            if (! File::isDirectory(public_path('kendala'))) {
                File::makeDirectory(public_path('kendala'), 0755, true);
            }
            $file = $request->file('bukti');
            $nama = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('kendala'), $nama);
            $data['bukti'] = $nama;
        }

        KendalaLaporan::create($data);

        return redirect()->route('penghuni.kendala.index')->with('status', 'Laporan kendala dikirim.');
    }
}
