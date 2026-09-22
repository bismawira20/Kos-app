<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $penghuni_id
 * @property int $kamar_id
 * @property int $tahun
 * @property int $bulan
 * @property int $jumlah
 * @property \Carbon\Carbon|null $jatuh_tempo
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Penghuni|null $penghuni
 * @property \App\Models\Kamar|null $kamar
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pembayaran> $pembayaran
 * @property bool|null $is_tunggakan
 * @property bool|null $is_menunggu_generate
 */
class Tagihan extends Model
{
    protected $fillable = [
        'penghuni_id',
        'kamar_id',
        'tahun',
        'bulan',
        'jumlah',
        'jatuh_tempo',
        'status',
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
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
}
