<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->unsignedInteger('hari_toleransi')->default(21)->after('durasi');
        });

        Schema::table('tagihans', function (Blueprint $table) {
            $table->date('batas_toleransi')->nullable()->after('jatuh_tempo');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement("UPDATE tagihans SET batas_toleransi = date(jatuh_tempo, '+21 days')");
        } else {
            DB::statement("UPDATE tagihans SET batas_toleransi = DATE_ADD(jatuh_tempo, INTERVAL 21 DAY)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropColumn('batas_toleransi');
        });

        Schema::table('kontraks', function (Blueprint $table) {
            $table->dropColumn('hari_toleransi');
        });
    }
};
