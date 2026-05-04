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
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->string('order_id')->nullable()->after('id');
            $table->string('snap_token')->nullable()->after('status');
            $table->string('metode_pembayaran')->nullable()->after('snap_token')->comment('midtrans / manual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'snap_token', 'metode_pembayaran']);
        });
    }
};
