<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaliMurid extends Model
{
    use HasFactory;

    // Nama tabel yang terkait dengan model
    protected $table = 'wali_murid';

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'murid_id',
        'nama_ayah',
        'pekerjaan_ayah',
        'pendidikan_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'pendidikan_ibu',
        'telepon_wali',
        'alamat_wali',
    ];

    /**
     * Relasi One-to-One: WaliMurid memiliki satu Murid.
     */
    public function murid()
    {
        return $this->belongsTo(Murid::class, 'murid_id');
    }
}