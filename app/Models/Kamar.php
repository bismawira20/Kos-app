<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $fillable = ['nomor_kamar', 'harga', 'status', 'tipe_kamar_id'];

    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class);
    }

    public function penghuni()
    {
        return $this->hasOne(Penghuni::class);
    }
}
