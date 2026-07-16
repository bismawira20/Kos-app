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
        Schema::table('kendala_laporans', function (Blueprint $table) {
            $table->text('feedback_penghuni')->nullable()->after('catatan_admin');
            $table->timestamp('diperbaiki_at')->nullable()->after('ditinjau_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kendala_laporans', function (Blueprint $table) {
            $table->dropColumn(['feedback_penghuni', 'diperbaiki_at']);
        });
    }
};
