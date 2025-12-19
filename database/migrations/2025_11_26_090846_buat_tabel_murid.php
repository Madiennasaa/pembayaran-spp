<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('murid', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users
            // Saat user dihapus, data murid ikut terhapus (cascade)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // NISN tetap ada sebagai identitas unik siswa & relasi ke pembayaran
            $table->string('nisn')->unique(); 
            
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']); 
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            
            // HAPUS kolom password dari sini karena sudah ada di tabel users
            // $table->string('password'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murid');
    }
};