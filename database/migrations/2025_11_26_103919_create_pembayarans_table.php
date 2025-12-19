<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();

            // Kolom NISN
            $table->string('nisn');
            $table->foreign('nisn')->references('nisn')->on('murid')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('bulan_spp', 20);
            $table->year('tahun_spp');
            $table->date('tanggal_bayar');

            // Menggunakan unsignedBigInteger untuk nominal mata uang bulat (Rp50000)
            $table->unsignedBigInteger('jumlah_bayar');

            // Hanya transfer dan qris
            $table->enum('metode_pembayaran', ['transfer', 'qris']);
            $table->string('bukti_transfer')->nullable();
            
            // 1. Status kembali sederhana
            $table->enum('status', ['pending', 'lunas', 'ditolak'])->default('pending');

            // 2. Kolom baru untuk menyimpan alasan (misal: "Bukti Buram", "Nominal Salah")
            // Dibuat nullable karena jika status 'pending' atau 'lunas', ini tidak perlu diisi.
            $table->string('alasan_penolakan')->nullable();

            $table->timestamps();
            $table->unique(['nisn', 'bulan_spp', 'tahun_spp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
