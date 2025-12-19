<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tetap diimport
use App\Models\Murid;
use App\Models\WaliMurid;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // Tambahkan DB untuk transaksi jika diperlukan
use Illuminate\Support\Facades\Hash; // Tambahkan Hash

class ProfileController extends Controller
    {
    /**
     * Tampilkan halaman profil murid dan walinya berdasarkan ID Murid.
     * @param string $id ID Murid yang akan diedit
     */
    public function edit(string $id) 
    {
        // Memperbaiki: Ambil data Murid berdasarkan ID dari parameter route
        $murid = Murid::with('wali')->findOrFail($id);

        if (strtolower(Auth::user()->role ?? '') === 'wali' && $murid->user_id !== Auth::id()) {
            return redirect()->route('pembayaran.index')->with('error', 'Tidak berhak mengakses profil ini.');
        }

        // WaliMurid mungkin belum ada (null), jadi buat jika belum ada
        if (!$murid->wali) {
            $murid->wali()->create(['murid_id' => $murid->id]);
            // Muat ulang relasi wali setelah dibuat
            $murid->load('wali'); 
        }

        // Catatan: Menggunakan view('profile.index') sesuai penamaan file Anda.
        return view('profile.index', compact('murid'));
    }

    /**
     * Proses pembaruan data murid dan orang tua.
     * @param Request $request
     * @param string $id ID Murid yang akan diupdate
     */
    public function update(Request $request, string $id)
    {
        // Memperbaiki: Ambil data Murid berdasarkan ID dari parameter route
        $murid = Murid::with('wali')->findOrFail($id);

        if (strtolower(Auth::user()->role ?? '') === 'wali' && $murid->user_id !== Auth::id()) {
            return redirect()->route('pembayaran.index')->with('error', 'Tidak berhak memperbarui profil ini.');
        }
        
        // Ambil data User yang terkait dengan Murid untuk validasi NISN
        $user = $murid->user;
        
        // Ambil data Wali Murid (pastikan sudah ada)
        $wali = $murid->wali;

        if (!$user) {
            return redirect()->back()->with('error', 'Data akun user tidak ditemukan untuk murid ini.');
        }

        if (!$wali) {
             // Buat Wali Murid jika ternyata belum ada (walaupun sudah dicek di edit)
             $wali = $murid->wali()->create(['murid_id' => $murid->id]);
        }

        // 1. Validasi Data
        $request->validate([
            // Validasi Unique NISN: Cek ke tabel USERS (username) dan ignore ID milik User terkait.
            'nisn' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            
            // Data Wali Murid
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:255'],
            'pendidikan_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:255'],
            'pendidikan_ibu' => ['nullable', 'string', 'max:255'],
            'telepon_wali' => ['nullable', 'string', 'max:20'],
            'alamat_wali' => ['nullable', 'string'],
            
            // Validasi password untuk user
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        try {
            DB::transaction(function () use ($request, $murid, $user, $wali) {
                
                // A. Update Akun User (Nama dan NISN/Username)
                $userData = [
                    'name' => $request->nama_lengkap,
                    'username' => $request->nisn,
                ];

                // Jika password diisi, hash dan tambahkan ke data user
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                $user->update($userData);

                // B. Update Data Murid (Biodata)
                $murid->update($request->only([
                    'nisn', 
                    'nama_lengkap', 
                    'jenis_kelamin', 
                    'tempat_lahir', 
                    'tanggal_lahir', 
                    'alamat'
                ]));

                // C. Update Data Wali Murid
                $waliData = $request->only([
                    'nama_ayah', 
                    'pekerjaan_ayah', 
                    'pendidikan_ayah',
                    'nama_ibu', 
                    'pekerjaan_ibu', 
                    'pendidikan_ibu',
                    'telepon_wali', 
                    'alamat_wali'
                ]);
                
                $wali->update($waliData);
            });

            $role = strtolower(Auth::user()->role ?? '');
            if ($role === 'wali') {
                return redirect()->route('pembayaran.index')->with('success', 'Profil berhasil diperbarui!');
            }
            return redirect()->route('murid.index')->with('success', 'Profil berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
