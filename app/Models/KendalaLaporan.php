<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KendalaLaporan extends Model
{
    protected $fillable = [
        'penghuni_id',
        'deskripsi',
        'bukti',
        'status',
        'alasan_tolak',
        'catatan_admin',
        'ditinjau_at',
    ];

    protected $casts = [
        'ditinjau_at' => 'datetime',
    ];

    public function penghuni(): BelongsTo
    {
        return $this->belongsTo(Penghuni::class);
    }
}
