<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            $table->string('nama_wali')->nullable()->after('no_hp');
            $table->string('no_hp_wali')->nullable()->after('nama_wali');
            $table->text('alamat_wali')->nullable()->after('no_hp_wali');
        });
    }

    public function down(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            $table->dropColumn(['nama_wali', 'no_hp_wali', 'alamat_wali']);
        });
    }
};
