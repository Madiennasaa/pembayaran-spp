<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('wali_murid', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel Murid (One-to-One)
            // Setiap murid HANYA memiliki SATU set data wali.
            $table->foreignId('murid_id')->unique()->constrained('murid')->onDelete('cascade');

            // Data Ayah
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            
            // Data Ibu
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();

            // Data Kontak Wali/Orang Tua
            $table->string('telepon_wali')->nullable();
            $table->text('alamat_wali')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('wali_murid');
    }
};