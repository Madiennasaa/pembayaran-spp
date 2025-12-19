<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Murid extends Model
{
    use HasFactory;
    
    protected $table = 'murid';
    
    protected $fillable = [
        'user_id',
        'nisn',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        // 'password', // Dihapus: Password diurus oleh model User.
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi One-to-One: Murid memiliki satu data WaliMurid.
     */
    public function wali()
    {
        return $this->hasOne(WaliMurid::class, 'murid_id');
    }

    /**
     * Relasi One-to-One: Murid memiliki satu User (akun login).
     */
    public function user() 
    {
        // Pastikan nama foreign key di tabel 'murid' adalah 'user_id' jika berbeda dari konvensi
        return $this->belongsTo(User::class, 'user_id'); 
    }

    /**
     * Relasi One-to-Many: Murid memiliki banyak data Pembayaran.
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Accessor untuk mendapatkan format TTL (Tempat, Tanggal Lahir).
     * Gunakan accessor $murid->ttl
     */
    public function getTtlAttribute()
    {
        // Pastikan kolom tanggal_lahir terisi sebelum format
        if (!$this->tanggal_lahir) {
            return $this->tempat_lahir . ', -';
        }
        
        // Asumsi format 'd F Y' adalah format tanggal Indonesia
        return $this->tempat_lahir . ', ' . Carbon::parse($this->tanggal_lahir)->translatedFormat('d F Y');
    }
}