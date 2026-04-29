<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Http;
use App\Models\Pembayaran;
use Carbon\Carbon;

Schedule::call(function () {

    $today = Carbon::today();

    $pembayaran = \App\Models\Pembayaran::with('penghuni')
        ->where('status', 'belum')
        ->get();

    foreach ($pembayaran as $p) {

        $penghuni = $p->penghuni;

        if (!$penghuni || !$penghuni->jatuh_tempo) continue;

        // 🚫 skip kalau sudah kirim hari ini
        if ($p->last_reminder_date == $today->toDateString()) continue;

        $jatuhTempo = Carbon::create(
            $today->year,
            $today->month,
            $penghuni->jatuh_tempo
        );

        $reminderDay = $jatuhTempo->copy()->subDay();

        $message = null;

        if ($today->isSameDay($reminderDay)) {
            $message = "Halo {$penghuni->nama} 👋
        Reminder: besok jatuh tempo pembayaran kos Rp{$p->jumlah} ya 🙏";
        } 
        elseif ($today->isSameDay($jatuhTempo)) {
            $message = "Halo {$penghuni->nama} 👋
        Hari ini jatuh tempo pembayaran kos Rp{$p->jumlah} 💰";
        } 
        elseif ($today->greaterThan($jatuhTempo)) {
            $telat = $jatuhTempo->diffInDays($today);

            $message = "Halo {$penghuni->nama} 👋
        Pembayaran kos kamu sudah terlambat {$telat} hari ⚠️
        Mohon segera dibayarkan Rp{$p->jumlah} 🙏";
        }

        if ($message) {

            Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])->post('https://api.fonnte.com/send', [
                'target' => $penghuni->no_hp,
                'message' => $message,
            ]);

            $p->last_reminder_date = $today->toDateString();
            $p->save();

            sleep(5);
        }
    }

})->daily();