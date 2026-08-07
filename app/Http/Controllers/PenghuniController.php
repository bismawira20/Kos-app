<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\Tagihan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenghuniController extends Controller
{
    public function index(Request $request): View
    {
        $penghuni = Penghuni::with(['kamar', 'user'])->orderBy('nama')->get();

        return view('penghuni.index', compact('penghuni'));
    }

    public function create(): View
    {
        // Hanya menampilkan kamar yang masih kosong
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
            'user_id' => ['required', 'exists:users,id'],
            'no_hp' => ['required', 'string', 'digits_between:10,13'],
            'kamar_id' => ['required', 'exists:kamars,id'],
            'tanggal_masuk' => ['required', 'date'],
            'nama_wali' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp_wali' => ['nullable', 'string', 'digits_between:10,13'],
            'alamat_wali' => ['nullable', 'string'],
            'hubungan' => ['nullable', 'string', 'in:Ayah,Ibu,Saudara,Suami,Istri,Teman,Lainnya'],
        ], [
            'nama_wali.regex' => 'Nama kontak darurat hanya boleh berisi huruf dan spasi.',
            'no_hp.digits_between' => 'Nomor HP harus berupa angka dengan panjang antara 10 hingga 13 digit.',
            'no_hp_wali.digits_between' => 'Nomor HP kontak darurat harus berupa angka dengan panjang antara 10 hingga 13 digit.',
        ]);

        $kamar = Kamar::findOrFail($validated['kamar_id']);
        $isOccupied = Penghuni::where('kamar_id', $validated['kamar_id'])->exists();

        if ($kamar->status !== 'kosong' || $isOccupied) {
            return back()->withInput()->with('error', 'Kamar yang dipilih sudah ditempati penghuni lain. Silakan pilih kamar yang masih kosong.');
        }

        $user = User::find($validated['user_id']);
        if (!$user || $user->role !== 'penghuni' || $user->penghuni) {
            return back()->withInput()->with('error', 'Akun penghuni tidak valid atau sudah terhubung.');
        }

        // Masa sewa awal wajib 12 bulan (2 tahap @ 6 bulan)
        $start = $validated['tanggal_masuk'];
        $duration = 12;
        $end = Carbon::parse($start)->addMonths(12)->subDay()->toDateString();

        DB::transaction(function () use ($validated, $user, $start, $duration, $end) {
            $penghuni = Penghuni::create([
                'nama' => $user->name,
                'no_hp' => $validated['no_hp'],
                'kamar_id' => $validated['kamar_id'],
                'user_id' => $user->id,
                'tanggal_masuk' => $start,
                'tanggal_selesai' => $end,
                'durasi_kontrak' => $duration,
                'nama_wali' => $validated['nama_wali'] ?? null,
                'no_hp_wali' => $validated['no_hp_wali'] ?? null,
                'alamat_wali' => $validated['alamat_wali'] ?? null,
                'hubungan' => $validated['hubungan'] ?? null,
            ]);

            $penghuni->generateBilling($start, $duration);

            Kamar::where('id', $validated['kamar_id'])->update(['status' => 'terisi']);
        });

        return redirect()->route('penghuni.index')->with('status', 'Penghuni berhasil didaftarkan dengan masa sewa awal 12 bulan (2 tahap @ 6 bulan).');
    }

    public function destroy(Penghuni $penghuni): RedirectResponse
    {
        // 1 & 2: Penghuni hanya dapat dihapus apabila tidak memiliki tagihan aktif ("Belum Bayar", "Menunggu Verifikasi", dll)
        $unfinishedBillsCount = Tagihan::where('penghuni_id', $penghuni->id)
            ->where('status', '!=', 'lunas')
            ->count();

        if ($unfinishedBillsCount > 0) {
            return back()->with('error', 'Data penghuni tidak dapat dihapus karena masih memiliki tagihan yang belum diselesaikan.');
        }

        // 3 & 4: Hapus penghuni, kosongkan kamar, dan hapus akun terhubung
        DB::transaction(function () use ($penghuni) {
            // Status kamar otomatis berubah menjadi "Kosong"
            Kamar::where('id', $penghuni->kamar_id)->update(['status' => 'kosong']);

            $userId = $penghuni->user_id;

            // Hapus data tagihan (lunas) & data penghuni
            Tagihan::where('penghuni_id', $penghuni->id)->delete();
            $penghuni->delete();

            // Akun yang terhubung juga ikut dihapus
            if ($userId) {
                User::where('id', $userId)->delete();
            }
        });

        return redirect()->route('penghuni.index')->with('status', 'Data penghuni berhasil dihapus.');
    }

    public function edit(Penghuni $penghuni): View
    {
        // Hanya menampilkan kamar yang masih berstatus "Kosong" serta kamar yang saat ini ditempati penghuni tersebut
        $kamar = Kamar::where('status', 'kosong')
            ->orWhere('id', $penghuni->kamar_id)
            ->orderBy('nomor_kamar')
            ->get();

        $users = User::where('role', 'penghuni')
            ->where(function ($query) use ($penghuni) {
                $query->whereDoesntHave('penghuni')
                    ->orWhere('id', $penghuni->user_id);
            })
            ->orderBy('name')
            ->get();

        return view('penghuni.edit', compact('penghuni', 'kamar', 'users'));
    }

    public function update(Request $request, Penghuni $penghuni): RedirectResponse
    {
        $isLama = $penghuni->isPenghuniLama();

        $rules = [
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp' => ['required', 'string', 'digits_between:10,13'],
            'kamar_id' => ['required', 'exists:kamars,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'tanggal_masuk' => ['required', 'date'],
            'nama_wali' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp_wali' => ['nullable', 'string', 'digits_between:10,13'],
            'alamat_wali' => ['nullable', 'string'],
            'hubungan' => ['nullable', 'string', 'in:Ayah,Ibu,Saudara,Suami,Istri,Teman,Lainnya'],
        ];

        if ($isLama) {
            $rules['durasi_kontrak'] = ['required', 'integer', 'in:3,6'];
        }

        $validated = $request->validate($rules, [
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'no_hp.digits_between' => 'Nomor HP harus berupa angka dengan panjang antara 10 hingga 13 digit.',
            'nama_wali.regex' => 'Nama kontak darurat hanya boleh berisi huruf dan spasi.',
            'no_hp_wali.digits_between' => 'Nomor HP kontak darurat harus berupa angka dengan panjang antara 10 hingga 13 digit.',
        ]);

        $oldKamarId = $penghuni->kamar_id;
        $newKamarId = (int) $validated['kamar_id'];

        if ($oldKamarId != $newKamarId) {
            $newKamar = Kamar::find($newKamarId);
            $isOccupiedByOther = Penghuni::where('kamar_id', $newKamarId)
                ->where('id', '!=', $penghuni->id)
                ->exists();

            if (!$newKamar || $newKamar->status !== 'kosong' || $isOccupiedByOther) {
                return back()->withInput()->with('error', 'Kamar yang dipilih sudah ditempati penghuni lain. Silakan pilih kamar yang masih kosong.');
            }
        }

        if (!empty($validated['user_id']) && $validated['user_id'] != $penghuni->user_id) {
            $user = User::find($validated['user_id']);
            if (!$user || $user->role !== 'penghuni' || $user->penghuni) {
                return back()->withInput()->with('error', 'Akun penghuni tidak valid atau sudah terhubung.');
            }
        }

        $start = $validated['tanggal_masuk'];
        $duration = $isLama ? (int) $validated['durasi_kontrak'] : 12;
        $end = Carbon::parse($start)->addMonths($duration)->subDay()->toDateString();

        DB::transaction(function () use ($validated, $penghuni, $oldKamarId, $newKamarId, $start, $duration, $end) {
            $penghuni->update([
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'kamar_id' => $newKamarId,
                'user_id' => $validated['user_id'] ?? null,
                'tanggal_masuk' => $start,
                'tanggal_selesai' => $end,
                'durasi_kontrak' => $duration,
                'nama_wali' => $validated['nama_wali'] ?? null,
                'no_hp_wali' => $validated['no_hp_wali'] ?? null,
                'alamat_wali' => $validated['alamat_wali'] ?? null,
                'hubungan' => $validated['hubungan'] ?? null,
            ]);

            Tagihan::where('penghuni_id', $penghuni->id)
                ->where('status', 'menunggu_generate')
                ->delete();

            $penghuni->generateBilling($start, $duration);

            if ($oldKamarId != $newKamarId) {
                Kamar::where('id', $oldKamarId)->update(['status' => 'kosong']);
                Kamar::where('id', $newKamarId)->update(['status' => 'terisi']);

                Tagihan::where('penghuni_id', $penghuni->id)
                    ->whereIn('status', ['menunggu_generate', 'belum_bayar', 'menunggu'])
                    ->update(['kamar_id' => $newKamarId]);
            }
        });

        return redirect()->route('penghuni.index')->with('status', 'Data penghuni berhasil diperbarui.');
    }
}
