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
        'feedback_penghuni',
        'ditinjau_at',
        'diperbaiki_at',
    ];

    protected $casts = [
        'ditinjau_at' => 'datetime',
        'diperbaiki_at' => 'datetime',
    ];

    public static function autoResolveOverdue(): void
    {
        self::where('status', 'diperbaiki')
            ->where('diperbaiki_at', '<=', now()->subDay())
            ->update([
                'status' => 'selesai',
                'updated_at' => now(),
            ]);
    }

    public function penghuni(): BelongsTo
    {
        return $this->belongsTo(Penghuni::class);
    }
}
