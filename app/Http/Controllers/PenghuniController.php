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
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PenghuniController extends Controller
{
    public function index(Request $request): View
    {
        $penghuni = Penghuni::with(['kamar', 'user', 'tagihan'])->orderBy('nama')->get();

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
            'no_hp' => ['required', 'string', 'digits_between:10,13', 'unique:penghunis,no_hp'],
            'alamat' => ['nullable', 'string'],
            'kamar_id' => ['required', 'exists:kamars,id'],
            'tanggal_masuk' => ['required', 'date'],
            'nama_wali' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp_wali' => ['nullable', 'string', 'digits_between:10,13'],
            'alamat_wali' => ['nullable', 'string'],
            'hubungan' => ['nullable', 'string', 'in:Ayah,Ibu,Saudara,Suami,Istri,Teman,Lainnya'],
        ], [
            'nama_wali.regex' => 'Nama kontak darurat hanya boleh berisi huruf dan spasi.',
            'no_hp.digits_between' => 'Nomor HP harus berupa angka dengan panjang antara 10 hingga 13 digit.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar. Silakan gunakan nomor HP lain.',
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
        $hargaKontrak = $kamar->harga; // Simpan harga sewa yang berlaku saat pendaftaran awal

        DB::transaction(function () use ($validated, $user, $start, $duration, $end, $hargaKontrak) {
            $penghuni = Penghuni::create([
                'nama' => $user->name,
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'] ?? null,
                'kamar_id' => $validated['kamar_id'],
                'harga_kontrak' => $hargaKontrak,
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

    /**
     * Aksi khusus perpanjangan masa sewa untuk Penghuni Lama.
     */
    public function perpanjang(Request $request, Penghuni $penghuni): RedirectResponse
    {
        if (!$penghuni->isPenghuniLama()) {
            return back()->with('error', 'Perpanjangan masa sewa hanya dapat dilakukan untuk penghuni lama.');
        }

        $hasUnpaidBills = Tagihan::where('penghuni_id', $penghuni->id)
            ->whereIn('status', ['belum_bayar', 'menunggu', 'ditolak'])
            ->exists();

        if ($hasUnpaidBills) {
            return back()->with('error', 'Perpanjangan masa sewa tidak dapat dilakukan karena masih terdapat tagihan yang belum diselesaikan.');
        }

        $validated = $request->validate([
            'durasi_kontrak' => ['required', 'integer', 'in:3,6,12'],
        ]);

        $duration = (int) $validated['durasi_kontrak'];

        // Tanggal mulai sewa baru otomatis 1 hari setelah tanggal berakhir kontrak sebelumnya
        $start = $penghuni->tanggal_selesai ? $penghuni->tanggal_selesai->copy()->addDay() : now()->startOfDay();
        $end = $start->copy()->addMonths($duration)->subDay()->toDateString();

        // Ambil harga kamar terbaru saat perpanjangan dilakukan
        $penghuni->load('kamar');
        $hargaKontrakBaru = $penghuni->kamar ? $penghuni->kamar->harga : $penghuni->harga_sewa_effective;

        DB::transaction(function () use ($penghuni, $start, $end, $duration, $hargaKontrakBaru) {
            $penghuni->update([
                'tanggal_masuk' => $start->toDateString(),
                'tanggal_selesai' => $end,
                'durasi_kontrak' => $duration,
                'harga_kontrak' => $hargaKontrakBaru,
            ]);

            // Riwayat tagihan dan pembayaran lama tetap dipertahankan
            Tagihan::where('penghuni_id', $penghuni->id)
                ->where('status', 'menunggu_generate')
                ->delete();

            $penghuni->generateBilling($start->toDateString(), $duration);
        });

        return redirect()->route('penghuni.index')->with('status', "Perpanjangan masa sewa berhasil diperbarui untuk durasi {$duration} bulan.");
    }

    public function destroy(Penghuni $penghuni): RedirectResponse
    {
        $unfinishedBillsCount = Tagihan::where('penghuni_id', $penghuni->id)
            ->where('status', '!=', 'lunas')
            ->count();

        if ($unfinishedBillsCount > 0) {
            return back()->with('error', 'Data penghuni tidak dapat dihapus karena masih memiliki tagihan yang belum diselesaikan.');
        }

        DB::transaction(function () use ($penghuni) {
            Kamar::where('id', $penghuni->kamar_id)->update(['status' => 'kosong']);

            $userId = $penghuni->user_id;

            Tagihan::where('penghuni_id', $penghuni->id)->delete();
            $penghuni->delete();

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

    /**
     * Memperbarui data identitas penghuni (nama, no_hp, alamat, kamar, wali, user).
     */
    public function update(Request $request, Penghuni $penghuni): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp' => ['required', 'string', 'digits_between:10,13', Rule::unique('penghunis', 'no_hp')->ignore($penghuni->id)],
            'alamat' => ['nullable', 'string'],
            'kamar_id' => ['required', 'exists:kamars,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'nama_wali' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp_wali' => ['nullable', 'string', 'digits_between:10,13'],
            'alamat_wali' => ['nullable', 'string'],
            'hubungan' => ['nullable', 'string', 'in:Ayah,Ibu,Saudara,Suami,Istri,Teman,Lainnya'],
        ], [
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'no_hp.digits_between' => 'Nomor HP harus berupa angka dengan panjang antara 10 hingga 13 digit.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar. Silakan gunakan nomor HP lain.',
            'nama_wali.regex' => 'Nama kontak darurat hanya boleh berisi huruf dan spasi.',
            'no_hp_wali.digits_between' => 'Nomor HP kontak darurat harus berupa angka dengan panjang antara 10 hingga 13 digit.',
        ]);

        $oldKamarId = $penghuni->kamar_id;
        $newKamarId = (int) $validated['kamar_id'];
        $targetKamar = Kamar::findOrFail($newKamarId);

        if ($oldKamarId != $newKamarId) {
            $isOccupiedByOther = Penghuni::where('kamar_id', $newKamarId)
                ->where('id', '!=', $penghuni->id)
                ->exists();

            if ($targetKamar->status !== 'kosong' || $isOccupiedByOther) {
                return back()->withInput()->with('error', 'Kamar yang dipilih sudah ditempati penghuni lain. Silakan pilih kamar yang masih kosong.');
            }
        }

        if (!empty($validated['user_id']) && $validated['user_id'] != $penghuni->user_id) {
            $user = User::find($validated['user_id']);
            if (!$user || $user->role !== 'penghuni' || $user->penghuni) {
                return back()->withInput()->with('error', 'Akun penghuni tidak valid atau sudah terhubung.');
            }
        }

        $hargaKontrak = ($oldKamarId != $newKamarId) ? $targetKamar->harga : ($penghuni->harga_kontrak ?? $targetKamar->harga);

        DB::transaction(function () use ($validated, $penghuni, $oldKamarId, $newKamarId, $hargaKontrak) {
            $penghuni->update([
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'] ?? null,
                'kamar_id' => $newKamarId,
                'harga_kontrak' => $hargaKontrak,
                'user_id' => $validated['user_id'] ?? null,
                'nama_wali' => $validated['nama_wali'] ?? null,
                'no_hp_wali' => $validated['no_hp_wali'] ?? null,
                'alamat_wali' => $validated['alamat_wali'] ?? null,
                'hubungan' => $validated['hubungan'] ?? null,
            ]);

            if ($oldKamarId != $newKamarId) {
                Kamar::where('id', $oldKamarId)->update(['status' => 'kosong']);
                Kamar::where('id', $newKamarId)->update(['status' => 'terisi']);

                Tagihan::where('penghuni_id', $penghuni->id)
                    ->whereIn('status', ['menunggu_generate', 'belum_bayar', 'menunggu'])
                    ->update(['kamar_id' => $newKamarId]);
            }
        });

        return redirect()->route('penghuni.index')->with('status', 'Data identitas penghuni berhasil diperbarui.');
    }
}
