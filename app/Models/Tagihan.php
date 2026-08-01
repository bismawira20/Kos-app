<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    protected $fillable = [
        'penghuni_id',
        'kamar_id',
        'tahun',
        'bulan',
        'jumlah',
        'jatuh_tempo',
        'batas_toleransi',
        'status',
        'melewati_toleransi',
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
        'batas_toleransi' => 'date',
        'melewati_toleransi' => 'boolean',
    ];

    public function penghuni(): BelongsTo
    {
        return $this->belongsTo(Penghuni::class);
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function labelPeriode(): string
    {
        $nama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return ($nama[$this->bulan] ?? $this->bulan).' '.$this->tahun;
    }

    public static function checkTolerance()
    {
        $today = now()->toDateString();
        
        self::where('status', 'belum_bayar')
            ->whereNotNull('batas_toleransi')
            ->where('batas_toleransi', '<', $today)
            ->update(['status' => 'melewati_batas_toleransi']);
    }
}
