<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\Kontrak;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenghuniController extends Controller
{
    public function index(): View
    {
        Kontrak::autoTransition();
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
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp' => ['required', 'string', 'digits_between:10,13'],
            'kamar_id' => ['required', 'exists:kamars,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'tanggal_masuk' => ['required', 'date'],
            'durasi_kontrak' => ['required', 'integer', 'min:1', 'max:120'],
            'hari_toleransi' => ['required', 'integer', 'min:0', 'max:120'],
            'nama_wali' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp_wali' => ['nullable', 'string', 'digits_between:10,13'],
            'alamat_wali' => ['nullable', 'string'],
            'hubungan' => ['nullable', 'string', 'in:Ayah,Ibu,Saudara,Suami,Istri,Teman,Lainnya'],
        ], [
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'no_hp.digits_between' => 'Nomor HP harus berupa angka dengan panjang antara 10 hingga 13 digit.',
            'nama_wali.regex' => 'Nama kontak darurat hanya boleh berisi huruf dan spasi.',
            'no_hp_wali.digits_between' => 'Nomor HP kontak darurat harus berupa angka dengan panjang antara 10 hingga 13 digit.',
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
            $penghuni = Penghuni::create([
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'kamar_id' => $validated['kamar_id'],
                'user_id' => $validated['user_id'] ?? null,
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? null,
                'durasi_kontrak' => $validated['durasi_kontrak'],
                'nama_wali' => $validated['nama_wali'],
                'no_hp_wali' => $validated['no_hp_wali'],
                'alamat_wali' => $validated['alamat_wali'],
                'hubungan' => $validated['hubungan'] ?? null,
            ]);

            // Create initial contract
            $start = $validated['tanggal_masuk'];
            $duration = $validated['durasi_kontrak'];
            $end = date('Y-m-d', strtotime("+{$duration} months -1 day", strtotime($start)));
            
            $kontrak = Kontrak::create([
                'penghuni_id' => $penghuni->id,
                'tanggal_mulai' => $start,
                'tanggal_berakhir' => $end,
                'durasi' => $duration,
                'hari_toleransi' => $validated['hari_toleransi'],
                'status' => 'aktif',
            ]);

            Kontrak::generateBillingForContract($kontrak);

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

    public function edit(Penghuni $penghuni): View
    {
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
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp' => ['required', 'string', 'digits_between:10,13'],
            'kamar_id' => ['required', 'exists:kamars,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'tanggal_masuk' => ['required', 'date'],
            'durasi_kontrak' => ['required', 'integer', 'min:1', 'max:120'],
            'hari_toleransi' => ['required', 'integer', 'min:0', 'max:120'],
            'nama_wali' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'no_hp_wali' => ['nullable', 'string', 'digits_between:10,13'],
            'alamat_wali' => ['nullable', 'string'],
            'hubungan' => ['nullable', 'string', 'in:Ayah,Ibu,Saudara,Suami,Istri,Teman,Lainnya'],
        ], [
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'no_hp.digits_between' => 'Nomor HP harus berupa angka dengan panjang antara 10 hingga 13 digit.',
            'nama_wali.regex' => 'Nama kontak darurat hanya boleh berisi huruf dan spasi.',
            'no_hp_wali.digits_between' => 'Nomor HP kontak darurat harus berupa angka dengan panjang antara 10 hingga 13 digit.',
        ]);

        $oldKamarId = $penghuni->kamar_id;
        $newKamarId = $validated['kamar_id'];

        if ($oldKamarId != $newKamarId) {
            $newKamar = Kamar::findOrFail($newKamarId);
            if ($newKamar->status !== 'kosong') {
                return back()->withInput()->with('error', 'Kamar yang baru dipilih sudah tidak tersedia.');
            }
        }

        if (! empty($validated['user_id']) && $validated['user_id'] != $penghuni->user_id) {
            $user = User::find($validated['user_id']);
            if (! $user || $user->role !== 'penghuni' || $user->penghuni) {
                return back()->withInput()->with('error', 'Akun penghuni tidak valid atau sudah terhubung.');
            }
        }

        DB::transaction(function () use ($validated, $penghuni, $oldKamarId, $newKamarId) {
            $penghuni->update([
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'kamar_id' => $newKamarId,
                'user_id' => $validated['user_id'] ?? null,
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? null,
                'durasi_kontrak' => $validated['durasi_kontrak'],
                'nama_wali' => $validated['nama_wali'],
                'no_hp_wali' => $validated['no_hp_wali'],
                'alamat_wali' => $validated['alamat_wali'],
                'hubungan' => $validated['hubungan'] ?? null,
            ]);

            // Update active contract details if exists
            $activeContract = $penghuni->kontraks()->where('status', 'aktif')->first();
            if ($activeContract) {
                $start = $validated['tanggal_masuk'];
                $duration = $validated['durasi_kontrak'];
                $end = date('Y-m-d', strtotime("+{$duration} months -1 day", strtotime($start)));
                $activeContract->update([
                    'tanggal_mulai' => $start,
                    'tanggal_berakhir' => $end,
                    'durasi' => $duration,
                    'hari_toleransi' => $validated['hari_toleransi'],
                ]);

                // Regenerate billing terms
                Tagihan::where('penghuni_id', $penghuni->id)
                    ->where('status', 'menunggu_generate')
                    ->delete();

                Kontrak::generateBillingForContract($activeContract);
            }

            if ($oldKamarId != $newKamarId) {
                Kamar::where('id', $oldKamarId)->update(['status' => 'kosong']);
                Kamar::where('id', $newKamarId)->update(['status' => 'terisi']);
            }
        });

        return redirect()->route('penghuni.index')->with('status', 'Data penghuni berhasil diperbarui.');
    }

    public function perpanjang(Request $request, Penghuni $penghuni): RedirectResponse
    {
        $validated = $request->validate([
            'durasi' => ['required', 'integer', 'in:3,6'],
            'hari_toleransi' => ['required', 'integer', 'min:0', 'max:120'],
        ]);

        $duration = (int) $validated['durasi'];
        $toleransi = (int) $validated['hari_toleransi'];

        DB::transaction(function () use ($penghuni, $duration, $toleransi) {
            // Find latest contract to determine start date
            $latestContract = Kontrak::where('penghuni_id', $penghuni->id)
                ->orderByDesc('tanggal_berakhir')
                ->first();

            $start = $latestContract
                ? \Carbon\Carbon::parse($latestContract->tanggal_berakhir)->addDay()->toDateString()
                : \Carbon\Carbon::parse($penghuni->tanggal_masuk ?? now())->toDateString();

            $end = \Carbon\Carbon::parse($start)->addMonths($duration)->subDay()->toDateString();

            // Check if there is an active contract currently
            $hasActive = Kontrak::where('penghuni_id', $penghuni->id)
                ->where('status', 'aktif')
                ->exists();

            $status = $hasActive ? 'menunggu_dimulai' : 'aktif';

            $newContract = Kontrak::create([
                'penghuni_id' => $penghuni->id,
                'tanggal_mulai' => $start,
                'tanggal_berakhir' => $end,
                'durasi' => $duration,
                'hari_toleransi' => $toleransi,
                'status' => $status,
            ]);

            // If it becomes active immediately, generate its billing schedules
            if ($status === 'aktif') {
                Kontrak::generateBillingForContract($newContract);
            }
        });

        return redirect()->route('penghuni.index')->with('status', 'Kontrak penghuni berhasil diperpanjang.');
    }
}
