<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    protected $fillable = ['nama', 'no_hp', 'kamar_id', 'user_id', 'nama_wali', 'no_hp_wali', 'alamat_wali', 'tanggal_masuk', 'hubungan', 'durasi_kontrak'];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class); //
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

    public function kontraks()
    {
        return $this->hasMany(Kontrak::class);
    }
}