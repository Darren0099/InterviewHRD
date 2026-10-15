<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kalau tabel lama (dari db_hrd.sql) sudah ada, data TIDAK dihapus.
        // Migrasi hanya menambahkan kolom waktu (created_at / updated_at) bila belum ada.
        if (Schema::hasTable('penilaian')) {
            if (! Schema::hasColumn('penilaian', 'created_at')) {
                Schema::table('penilaian', function (Blueprint $t) {
                    $t->timestamp('created_at')->nullable()->useCurrent();
                });
            }
            if (! Schema::hasColumn('penilaian', 'updated_at')) {
                Schema::table('penilaian', function (Blueprint $t) {
                    $t->timestamp('updated_at')->nullable();
                });
            }

            return;
        }

        Schema::create('penilaian', function (Blueprint $t) {
            $t->id();
            $t->string('regional', 100);
            $t->string('divisi', 100);
            $t->string('nama_kandidat', 150);
            $t->string('nama_hrd', 150);
            $t->unsignedTinyInteger('aspek_teknis')->default(0);
            $t->unsignedTinyInteger('aspek_komunikasi')->default(0);
            $t->unsignedTinyInteger('aspek_sikap')->default(0);
            $t->unsignedTinyInteger('aspek_motivasi')->default(0);
            $t->unsignedSmallInteger('total')->default(0);
            $t->text('catatan')->nullable();
            $t->timestamps();

            $t->index(['regional', 'divisi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
