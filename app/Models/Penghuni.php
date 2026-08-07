<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    protected $fillable = [
        'nama', 'no_hp', 'alamat', 'kamar_id', 'harga_kontrak', 'user_id', 'nama_wali', 'no_hp_wali',
        'alamat_wali', 'tanggal_masuk', 'tanggal_selesai', 'hubungan',
        'durasi_kontrak'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_selesai' => 'date',
        'harga_kontrak' => 'integer',
    ];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class); 
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    /**
     * Memeriksa apakah penghuni sudah tergolong "Penghuni Lama".
     */
    public function isPenghuniLama(): bool
    {
        $lunasCount = $this->tagihan()->where('status', 'lunas')->count();
        $monthsStayed = $this->tanggal_masuk ? (int) $this->tanggal_masuk->diffInMonths(now()) : 0;

        return $lunasCount >= 2 || $monthsStayed >= 12 || ($this->durasi_kontrak != 12 && $this->durasi_kontrak != null);
    }

    public function getStatusPenghuniAttribute(): string
    {
        return $this->isPenghuniLama() ? 'Penghuni Lama' : 'Penghuni Baru';
    }

    /**
     * Mengembalikan harga sewa efektif yang disepakati dalam kontrak penghuni.
     */
    public function getHargaSewaEffectiveAttribute(): int
    {
        return (int) ($this->harga_kontrak ?? $this->kamar?->harga ?? 0);
    }

    public function generateBilling($startDateString, $duration)
    {
        $this->load('kamar');
        if (!$this->kamar) {
            return;
        }

        $rentPrice = $this->harga_sewa_effective;
        $startDate = Carbon::parse($startDateString);
        $duration = (int) $duration;

        $step = ($duration === 12) ? 6 : $duration;

        for ($i = 0; $i < $duration; $i += $step) {
            $monthsInTerm = min($step, $duration - $i);
            $termStart = $startDate->copy()->addMonths($i);
            $due = $termStart->toDateString();
            $tahun = $termStart->year;
            $bulan = $termStart->month;
            $amount = $rentPrice * $monthsInTerm;

            $exists = Tagihan::where('penghuni_id', $this->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->exists();

            if (!$exists) {
                Tagihan::create([
                    'penghuni_id' => $this->id,
                    'kamar_id' => $this->kamar_id,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'jumlah' => $amount,
                    'jatuh_tempo' => $due,
                    'status' => 'menunggu_generate',
                ]);
            }
        }
    }
}