<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $penghuni_id
 * @property int $tagihan_id
 * @property string|null $order_id
 * @property string|null $snap_token
 * @property string|null $metode_pembayaran
 * @property int $jumlah
 * @property string $tanggal_bayar
 * @property string $status
 * @property string|null $bukti
 * @property string|null $admin_komentar
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Penghuni|null $penghuni
 * @property \App\Models\Tagihan|null $tagihan
 */
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
        'bukti',
        'admin_komentar',
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