<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Murid;
use App\Models\WaliMurid;
use Illuminate\Http\Request;
use App\Exports\MuridExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MuridController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data murid beserta relasi user-nya agar lebih efisien
        $query = Murid::with('user')->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        $murids = $query->paginate(10)->withQueryString();

        return view('murid.index', compact('murids'));
    }

    public function create()
    {
        return view('murid.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $validatedData = $request->validate([
            // PERBAIKAN: Cek unique ke tabel 'users' kolom 'username' (karena NISN jadi username)
            'nisn'            => 'required|numeric|unique:users,username',
            'nama_lengkap'    => 'required|string|max:255',
            'jenis_kelamin'   => 'required|in:L,P',
            'tempat_lahir'    => 'required|string',
            'tanggal_lahir'   => 'required|date',
            'alamat'          => 'required|string',
            'password'        => 'nullable|string|min:6',
            'nama_ayah'       => 'nullable|string|max:255',
            'pekerjaan_ayah'  => 'nullable|string|max:255',
            'pendidikan_ayah' => 'nullable|string|max:255',
            'nama_ibu'        => 'nullable|string|max:255',
            'pekerjaan_ibu'   => 'nullable|string|max:255',
            'pendidikan_ibu'  => 'nullable|string|max:255',
            'telepon_wali'    => 'nullable|string|max:20',
            'alamat_wali'     => 'nullable|string',
        ], [
            'nisn.unique'     => 'NISN ini sudah terdaftar !!!',
            'required'        => 'Kolom :attribute wajib diisi.',
        ]);

        // Gunakan Transaction untuk memastikan konsistensi data (User & Murid harus sukses dua-duanya)
        try {
            DB::transaction(function () use ($request, $validatedData) {

                // A. LOGIKA PASSWORD
                $passwordFix = $request->password ?? $request->nisn ?? '12345678';

                // B. BUAT AKUN USER (LOGIN)
                $user = User::create([
                    'name'     => $request->nama_lengkap,
                    'username' => $request->nisn,
                    'role'     => 'wali',
                    'password' => Hash::make($passwordFix),
                ]);

                // C. BUAT DATA MURID (BIODATA)
                $murid = Murid::create([
                    'user_id'       => $user->id,
                    'nisn'          => $request->nisn,
                    'nama_lengkap'  => $request->nama_lengkap,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir'  => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat'        => $request->alamat,
                ]);

                // D. BUAT DATA ORANG TUA/WALI TERKAIT MURID
                WaliMurid::create([
                    'murid_id'        => $murid->id,
                    'nama_ayah'       => $request->nama_ayah,
                    'pekerjaan_ayah'  => $request->pekerjaan_ayah,
                    'pendidikan_ayah' => $request->pendidikan_ayah,
                    'nama_ibu'        => $request->nama_ibu,
                    'pekerjaan_ibu'   => $request->pekerjaan_ibu,
                    'pendidikan_ibu'  => $request->pendidikan_ibu,
                    'telepon_wali'    => $request->telepon_wali,
                    'alamat_wali'     => $request->alamat_wali,
                ]);
            });

            return redirect()->route('murid.index')->with('success', 'Data murid dan akun login berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Jika error, kembalikan ke form dengan pesan error
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $murid = Murid::findOrFail($id);
        return view('murid.show', compact('murid'));
    }

    public function edit(string $id)
    {
        $murid = Murid::findOrFail($id);
        return view('murid.edit', compact('murid'));
    }

    public function update(Request $request, string $id)
    {
        $murid = Murid::with('user')->findOrFail($id);
        // Ambil data user yang terhubung dengan murid ini
        $user = $murid->user;

        // Jika data user tidak ditemukan (kasus langka/data rusak), kita handle errornya
        if (!$user) {
            return redirect()->back()->with('error', 'Data akun user tidak ditemukan untuk murid ini.');
        }

        $validatedData = $request->validate([
            'nisn'            => 'required|numeric|unique:users,username,' . $user->id,
            'nama_lengkap'    => 'required|string|max:255',
            'jenis_kelamin'   => 'required|in:L,P',
            'tempat_lahir'    => 'required|string',
            'tanggal_lahir'   => 'required|date',
            'alamat'          => 'required|string',
            'password'        => 'nullable|string|min:6'
        ]);

        try {
            DB::transaction(function () use ($request, $murid, $user) {

                // 1. UPDATE DATA USER (Login Info)
                $userData = [
                    'name'     => $request->nama_lengkap,
                    'username' => $request->nisn,
                ];

                // Cek apakah password diisi?
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $user->update($userData);

                // 2. UPDATE DATA MURID (Biodata)
                $murid->update([
                    'nisn'          => $request->nisn,
                    'nama_lengkap'  => $request->nama_lengkap,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir'  => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat'        => $request->alamat,
                ]);
            });

            return redirect()->route('murid.index')->with('success', 'Data murid dan akun login diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $murid = Murid::findOrFail($id);

        if ($murid->user) {
            $murid->user->delete();
        } else {
            // Fallback jika tidak ada relasi user (data lama/rusak), hapus murid saja
            $murid->delete();
        }

        return redirect()->route('murid.index')->with('success', 'Data murid dan akun login berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new MuridExport, 'laporan_data_murid.xlsx');
    }
}
