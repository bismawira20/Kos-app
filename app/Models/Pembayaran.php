<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = [
        'penghuni_id',
        'tagihan_id',
        'order_id',
        'snap_token',
        'metode_pembayaran',
        'jumlah',
        'tanggal_bayar',
        'status',
        'last_reminder_date',
        'bukti',
        'admin_komentar',
    ];

    protected $casts = [
        'last_reminder_date' => 'date',
    ];

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
}