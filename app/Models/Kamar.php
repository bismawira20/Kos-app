<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $fillable = ['nomor_kamar', 'harga', 'status'];
   public function penghuni()
{
    return $this->hasOne(Penghuni::class);
}
}
