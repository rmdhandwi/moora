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
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->string('user_id', 4)->primary();
            $table->string('username', 20)->unique();
            $table->text('password');
            $table->integer('role');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id', 4)->nullable();
            $table->foreign('user_id')->references('user_id')->on('tbl_users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Dosen
        Schema::create('tbl_dosen', function (Blueprint $table) {
            $table->string('dosen_id', 4)->primary();
            $table->string('nama_dosen');
            $table->string('user_id', 4);
            $table->foreign('user_id')->references('user_id')->on('tbl_users')->onDelete('cascade');
        });

        // Angkatan
        Schema::create('tbl_angkatan', function (Blueprint $table) {
            $table->string('angkatan_id', 4)->primary();
            $table->year('tahun_angkatan');
            $table->string('jurusan', 20);
        });

        // Mahasiswa
        Schema::create('tbl_mahasiswa', function (Blueprint $table) {
            $table->string('mahasiswa_id', 8)->primary();
            $table->string('nama_mahasiswa', 50);
            $table->string('npm', 8)->unique();
            $table->integer('total_sks');
            $table->integer('sks_tempuh');
            $table->integer('sks_sisa');
            $table->integer('total_studi');
            $table->integer('studi_sisa');
            $table->integer('studi_tempuh');
            $table->string('dosen_id', 4);
            $table->foreign('dosen_id')->references('dosen_id')->on('tbl_dosen')->onDelete('cascade');
            $table->string('angkatan_id', 4);
            $table->foreign('angkatan_id')->references('angkatan_id')->on('tbl_angkatan')->onDelete('cascade');
        });

        // Kriteria
        Schema::create('tbl_kriteria', function (Blueprint $table) {
            $table->string('kriteria_id')->primary();
            $table->string('kode_kriteria', 4);
            $table->string('nama_kriteria', 100)->unique();
            $table->float('bobot');
            $table->string('type', 8);
        });

        // Hasil
        Schema::create('tbl_hasil', function (Blueprint $table) {
            $table->string('hasil_id')->primary();
            $table->float('nilai_akhir');
            $table->integer('peringkat');
            $table->string('mahasiswa_id', 8);
            $table->foreign('mahasiswa_id')->references('mahasiswa_id')->on('tbl_mahasiswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_hasil');
        Schema::dropIfExists('tbl_kriteria');;
        Schema::dropIfExists('tbl_mahasiswa');
        Schema::dropIfExists('tbl_angkatan');
        Schema::dropIfExists('tbl_dosen');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('tbl_users');
    }
};
