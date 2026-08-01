<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kontraks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penghuni_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->unsignedInteger('durasi');
            $table->string('status', 24)->default('aktif'); // aktif, menunggu_dimulai, selesai
            $table->timestamps();
        });

        // Seed existing tenants
        $penghunis = DB::table('penghunis')->get();
        foreach ($penghunis as $p) {
            $start = $p->tanggal_masuk ?: date('Y-m-d');
            $duration = $p->durasi_kontrak ?: 12;
            $end = date('Y-m-d', strtotime("+{$duration} months -1 day", strtotime($start)));
            DB::table('kontraks')->insert([
                'penghuni_id' => $p->id,
                'tanggal_mulai' => $start,
                'tanggal_berakhir' => $end,
                'durasi' => $duration,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontraks');
    }
};
