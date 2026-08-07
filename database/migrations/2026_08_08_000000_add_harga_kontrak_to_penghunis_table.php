<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            $table->unsignedBigInteger('harga_kontrak')->nullable()->after('kamar_id');
        });

        // Populasikan harga_kontrak awal untuk data penghuni yang sudah ada berdasarkan harga kamar saat ini
        DB::statement("
            UPDATE penghunis 
            JOIN kamars ON penghunis.kamar_id = kamars.id 
            SET penghunis.harga_kontrak = kamars.harga 
            WHERE penghunis.harga_kontrak IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            $table->dropColumn('harga_kontrak');
        });
    }
};
