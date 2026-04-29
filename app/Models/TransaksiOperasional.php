<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiOperasional extends Model
{
    protected $fillable = [
        'tanggal',
        'jenis',
        'kategori',
        'deskripsi',
        'jumlah',
        'sumber',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
