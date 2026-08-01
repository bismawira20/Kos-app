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
            $table->boolean('melewati_toleransi')->default(false)->after('status');
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->boolean('melewati_toleransi')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn('melewati_toleransi');
        });

        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropColumn('melewati_toleransi');
        });
    }
};
