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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 32)->default('penghuni')->after('email');
            }
        });

        if (! Schema::hasColumn('penghunis', 'user_id')) {
            Schema::table('penghunis', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete()->unique();
            });
        }

        Schema::table('pembayarans', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayarans', 'bukti')) {
                $table->string('bukti')->nullable()->after('status');
            }
            if (! Schema::hasColumn('pembayarans', 'last_reminder_date')) {
                $table->date('last_reminder_date')->nullable()->after('bukti');
            }
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE pembayarans MODIFY COLUMN status ENUM('lunas', 'belum', 'ditolak') NOT NULL DEFAULT 'belum'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE pembayarans MODIFY COLUMN status ENUM('lunas', 'belum') NOT NULL DEFAULT 'belum'");
        }

        Schema::table('pembayarans', function (Blueprint $table) {
            if (Schema::hasColumn('pembayarans', 'last_reminder_date')) {
                $table->dropColumn('last_reminder_date');
            }
            if (Schema::hasColumn('pembayarans', 'bukti')) {
                $table->dropColumn('bukti');
            }
        });

        Schema::table('penghunis', function (Blueprint $table) {
            if (Schema::hasColumn('penghunis', 'user_id')) {
                $table->dropUnique(['user_id']);
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
