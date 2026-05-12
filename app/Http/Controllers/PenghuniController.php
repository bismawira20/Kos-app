<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenghuniController extends Controller
{
    public function index(): View
    {
        $penghuni = Penghuni::with(['kamar', 'user'])->orderBy('nama')->get();

        return view('penghuni.index', compact('penghuni'));
    }

    public function create(): View
    {
        $kamar = Kamar::where('status', 'kosong')->orderBy('nomor_kamar')->get();
        $users = User::where('role', 'penghuni')
            ->whereDoesntHave('penghuni')
            ->orderBy('name')
            ->get();

        return view('penghuni.create', compact('kamar', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:32'],
            'kamar_id' => ['required', 'exists:kamars,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'nama_wali' => ['nullable', 'string', 'max:255'],
            'no_hp_wali' => ['nullable', 'string', 'max:32'],
            'alamat_wali' => ['nullable', 'string'],
        ]);

        $kamar = Kamar::findOrFail($validated['kamar_id']);
        if ($kamar->status !== 'kosong') {
            return back()->withInput()->with('error', 'Kamar yang dipilih sudah tidak tersedia.');
        }

        if (! empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);
            if (! $user || $user->role !== 'penghuni' || $user->penghuni) {
                return back()->withInput()->with('error', 'Akun penghuni tidak valid atau sudah terhubung.');
            }
        }

        DB::transaction(function () use ($validated) {
            Penghuni::create([
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'kamar_id' => $validated['kamar_id'],
                'user_id' => $validated['user_id'] ?? null,
                'nama_wali' => $validated['nama_wali'],
                'no_hp_wali' => $validated['no_hp_wali'],
                'alamat_wali' => $validated['alamat_wali'],
            ]);

            Kamar::where('id', $validated['kamar_id'])->update(['status' => 'terisi']);
        });

        return redirect()->route('penghuni.index')->with('status', 'Penghuni berhasil didaftarkan.');
    }

    public function destroy(Penghuni $penghuni): RedirectResponse
    {
        DB::transaction(function () use ($penghuni) {
            Kamar::where('id', $penghuni->kamar_id)->update(['status' => 'kosong']);
            $penghuni->delete();
        });

        return redirect()->route('penghuni.index')->with('status', 'Data penghuni dihapus dan kamar dikosongkan.');
    }
}
