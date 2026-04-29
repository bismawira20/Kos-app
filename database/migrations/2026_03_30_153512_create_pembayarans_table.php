<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::create('pembayarans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('penghuni_id')->constrained()->onDelete('cascade');
        $table->integer('jumlah');
        $table->date('tanggal_bayar')->nullable();
        $table->enum('status', ['lunas', 'belum'])->default('belum');
        $table->timestamps();
    });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
