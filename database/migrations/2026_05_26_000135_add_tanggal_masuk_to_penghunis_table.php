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
        Schema::table('penghunis', function (Blueprint $table) {
            $table->date('tanggal_masuk')->nullable()->after('kamar_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            $table->dropColumn('tanggal_masuk');
        });
    }
};
