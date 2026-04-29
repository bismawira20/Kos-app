<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;
use App\Models\Pembayaran;

class Kernel extends ConsoleKernel
{
    protected function commands()
{
    $this->load(__DIR__.'/Commands');
}

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

        file_put_contents(storage_path('logs/test.txt'), "JALAN\n", FILE_APPEND);

            $pembayaran = \App\Models\Pembayaran::where('status', 'belum')->get();

            foreach ($pembayaran as $p) {
                $penghuni = $p->penghuni;

                if (!$penghuni) continue;

                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => env('FONNTE_TOKEN')
                ])->post('https://api.fonnte.com/send', [
                    'target' => $penghuni->no_hp,
                    'message' => "TEST REMINDER 🔥",
                ]);

                sleep(5);
            }

        })->cron('* * * * *');
    }
}