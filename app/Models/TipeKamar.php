<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipeKamar extends Model
{
    protected $fillable = ['nama', 'harga'];

    public function kamars(): HasMany
    {
        return $this->hasMany(Kamar::class);
    }
}
