<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Kontrak extends Model
{
    protected $fillable = [
        'penghuni_id',
        'tanggal_mulai',
        'tanggal_berakhir',
        'durasi',
        'status', // aktif, menunggu_dimulai, selesai
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    public function penghuni(): BelongsTo
    {
        return $this->belongsTo(Penghuni::class);
    }

    /**
     * Automatically transition contract statuses based on the current date.
     */
    public static function autoTransition()
    {
        $today = Carbon::today()->toDateString();

        // Find all pending contracts that should start today or in the past
        $pendingKontraks = self::where('status', 'menunggu_dimulai')
            ->where('tanggal_mulai', '<=', $today)
            ->get();

        foreach ($pendingKontraks as $k) {
            DB::transaction(function () use ($k) {
                // Deactivate the current active contract for this tenant
                self::where('penghuni_id', $k->penghuni_id)
                    ->where('status', 'aktif')
                    ->update(['status' => 'selesai']);

                // Activate the new contract
                $k->update(['status' => 'aktif']);

                // Generate billing schedule for this newly activated contract
                self::generateBillingForContract($k);
            });
        }
    }

    /**
     * Generate billing terms for an active contract.
     */
    public static function generateBillingForContract(Kontrak $contract)
    {
        $penghuni = $contract->penghuni;
        if (!$penghuni) {
            return;
        }

        $penghuni->load('kamar');
        $kamar = $penghuni->kamar;
        if (!$kamar) {
            return;
        }

        $startDate = Carbon::parse($contract->tanggal_mulai);
        $duration = (int) $contract->durasi;
        $hariToleransi = (int) ($contract->hari_toleransi ?? 21);

        // Loop to generate 6-month terms (or less if remaining duration is less than 6 months)
        for ($i = 0; $i < $duration; $i += 6) {
            $monthsInTerm = min(6, $duration - $i);
            $termStart = $startDate->copy()->addMonths($i);
            $due = $termStart->toDateString();
            $tahun = $termStart->year;
            $bulan = $termStart->month;
            $amount = $kamar->harga * $monthsInTerm;
            $batasToleransi = $termStart->copy()->addDays($hariToleransi)->toDateString();

            // Prevent duplicating bills for the same period
            $exists = Tagihan::where('penghuni_id', $penghuni->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->exists();

            if (!$exists) {
                Tagihan::create([
                    'penghuni_id' => $penghuni->id,
                    'kamar_id' => $penghuni->kamar_id,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'jumlah' => $amount,
                    'jatuh_tempo' => $due,
                    'batas_toleransi' => $batasToleransi,
                    'status' => 'menunggu_generate',
                ]);
            }
        }
    }
}
