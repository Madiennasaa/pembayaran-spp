<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    // Baris Kunci: Memberi tahu Laravel bahwa model ini menggunakan tabel 'pembayarans'
    protected $table = 'pembayarans'; 
    
    protected $fillable = [
        'no_transaksi',
        'nisn',
        'user_id',
        'bulan_spp',
        'tahun_spp',
        'jumlah_bayar',
        'metode_pembayaran',
        'tanggal_bayar',
        'bukti_transfer',
        'status',
    ];

    // Relasi ke tabel Murid
    public function murid()
    {
        return $this->belongsTo(Murid::class, 'nisn', 'nisn');
    }

    // Relasi ke tabel User
    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}