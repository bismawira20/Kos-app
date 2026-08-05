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
        Schema::table('tagihans', function (Blueprint $table) {
            if (Schema::hasColumn('tagihans', 'batas_toleransi')) {
                $table->dropColumn('batas_toleransi');
            }
            if (Schema::hasColumn('tagihans', 'melewati_toleransi')) {
                $table->dropColumn('melewati_toleransi');
            }
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            if (Schema::hasColumn('pembayarans', 'melewati_toleransi')) {
                $table->dropColumn('melewati_toleransi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->date('batas_toleransi')->nullable()->after('jatuh_tempo');
            $table->boolean('melewati_toleransi')->default(false)->after('status');
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->boolean('melewati_toleransi')->default(false)->after('status');
        });
    }
};
