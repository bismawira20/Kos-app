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
        Schema::create('tipe_kamars', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->unsignedInteger('harga');
            $table->timestamps();
        });

        // Seed default room types
        DB::table('tipe_kamars')->insert([
            [
                'nama' => 'AC',
                'harga' => 1350000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Non-AC',
                'harga' => 900000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('kamars', function (Blueprint $table) {
            $table->foreignId('tipe_kamar_id')->nullable()->after('id')->constrained('tipe_kamars')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kamars', function (Blueprint $table) {
            $table->dropForeign(['tipe_kamar_id']);
            $table->dropColumn('tipe_kamar_id');
        });

        Schema::dropIfExists('tipe_kamars');
    }
};
