<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kamars', function (Blueprint $table) {
            if (! Schema::hasColumn('kamars', 'lantai')) {
                $table->unsignedTinyInteger('lantai')->nullable()->after('harga');
            }
            if (! Schema::hasColumn('kamars', 'kapasitas')) {
                $table->unsignedTinyInteger('kapasitas')->default(2)->after('lantai');
            }
            if (! Schema::hasColumn('kamars', 'fasilitas')) {
                $table->text('fasilitas')->nullable()->after('kapasitas');
            }
        });

        if (! Schema::hasTable('tagihans')) {
            Schema::create('tagihans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('penghuni_id')->constrained()->cascadeOnDelete();
                $table->foreignId('kamar_id')->constrained()->cascadeOnDelete();
                $table->unsignedSmallInteger('tahun');
                $table->unsignedTinyInteger('bulan');
                $table->unsignedInteger('jumlah');
                $table->date('jatuh_tempo');
                $table->string('status', 24)->default('belum_bayar');
                $table->timestamps();

                $table->unique(['penghuni_id', 'tahun', 'bulan']);
            });
        }

        if (! Schema::hasTable('kendala_laporans')) {
            Schema::create('kendala_laporans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('penghuni_id')->constrained()->cascadeOnDelete();
                $table->text('deskripsi');
                $table->string('bukti')->nullable();
                $table->string('status', 24)->default('menunggu');
                $table->text('alasan_tolak')->nullable();
                $table->text('catatan_admin')->nullable();
                $table->timestamp('ditinjau_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('pembayarans', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayarans', 'tagihan_id')) {
                $table->foreignId('tagihan_id')->nullable()->after('penghuni_id')->constrained('tagihans')->nullOnDelete();
            }
            if (! Schema::hasColumn('pembayarans', 'admin_komentar')) {
                $table->text('admin_komentar')->nullable()->after('bukti');
            }
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE pembayarans MODIFY COLUMN status ENUM('lunas', 'belum', 'ditolak', 'menunggu') NOT NULL DEFAULT 'belum'");
        }
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            if (Schema::hasColumn('pembayarans', 'admin_komentar')) {
                $table->dropColumn('admin_komentar');
            }
            if (Schema::hasColumn('pembayarans', 'tagihan_id')) {
                $table->dropForeign(['tagihan_id']);
                $table->dropColumn('tagihan_id');
            }
        });

        Schema::dropIfExists('kendala_laporans');
        Schema::dropIfExists('tagihans');

        Schema::table('kamars', function (Blueprint $table) {
            if (Schema::hasColumn('kamars', 'fasilitas')) {
                $table->dropColumn('fasilitas');
            }
            if (Schema::hasColumn('kamars', 'kapasitas')) {
                $table->dropColumn('kapasitas');
            }
            if (Schema::hasColumn('kamars', 'lantai')) {
                $table->dropColumn('lantai');
            }
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE pembayarans MODIFY COLUMN status ENUM('lunas', 'belum', 'ditolak') NOT NULL DEFAULT 'belum'");
        }
    }
};
