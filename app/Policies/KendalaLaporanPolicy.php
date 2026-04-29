<?php

namespace App\Policies;

use App\Models\KendalaLaporan;
use App\Models\User;

class KendalaLaporanPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->role === 'admin' ? true : null;
    }

    public function view(User $user, KendalaLaporan $kendala): bool
    {
        return $user->penghuni?->id === $kendala->penghuni_id;
    }

    public function update(User $user, KendalaLaporan $kendala): bool
    {
        return $user->penghuni?->id === $kendala->penghuni_id && $kendala->status === 'menunggu';
    }

    public function delete(User $user, KendalaLaporan $kendala): bool
    {
        return $user->penghuni?->id === $kendala->penghuni_id && $kendala->status === 'menunggu';
    }
}
