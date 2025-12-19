<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Murid;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    /**
     * Menampilkan daftar pembayaran & Form Input dengan Logika Otomatis.
     */
    public function index()
    {
        // 1. Ambil NISN milik user yang login (berdasarkan relasi ke Murid)
        $muridLogin = Murid::where('user_id', Auth::id())->first();

        // 1a. Ambil data pembayaran hanya untuk NISN yang login
        $pembayarans = Pembayaran::with('murid')
            ->when($muridLogin && $muridLogin->nisn, function ($q) use ($muridLogin) {
                $q->where('nisn', $muridLogin->nisn);
            })
            ->latest()
            ->paginate(10);

        // 2. LOGIKA UTAMA: Hitung Tagihan Otomatis untuk Dropdown Murid
        $biayaSPP = 50000; // Default biaya SPP

        // Atur Carbon ke Bahasa Indonesia
        Carbon::setLocale('id');
        $bulanIni = Carbon::now()->translatedFormat('F');
        $tahunIni = date('Y');

        // Ambil murid milik user yang login (wali)
        $murids = Murid::where('user_id', Auth::id())->get()->map(function ($murid) use ($biayaSPP, $bulanIni, $tahunIni) {

            // Cek status bayar bulan ini (hanya hitung yang sudah LUNAS)
            $sudahBayar = Pembayaran::where('nisn', $murid->nisn)
                ->where('bulan_spp', $bulanIni)
                ->where('tahun_spp', $tahunIni)
                ->where('status', 'lunas')
                ->exists();

            $murid->tagihan_default = $biayaSPP;
            $murid->status_bulan_ini = $sudahBayar; // true/false
            $murid->next_bulan = $bulanIni;
            $murid->next_tahun = $tahunIni;

            return $murid;
        });

        return view('pembayaran.index', compact('pembayarans', 'murids'));
    }

    /**
     * Menyimpan pembayaran baru.
     */
    public function store(Request $request)
    {
        // VALIDASI
        $validatedData = $request->validate([
            'nisn' => 'required|exists:murid,nisn',
            'bulan_spp' => 'required|string',
            'tahun_spp' => 'required|digits:4',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:transfer,qris',
            'bukti_transfer' => 'nullable|image|file|max:2048',
        ]);

        $validatedData['tanggal_bayar'] = now()->format('Y-m-d');

        // Paksa NISN sesuai user login
        $muridLogin = Murid::where('user_id', Auth::id())->first();
        if ($muridLogin) {
            $validatedData['nisn'] = $muridLogin->nisn;
        }

        // CEK apakah sudah ada pembayaran bulan itu
        $pembayaran = Pembayaran::where('nisn', $validatedData['nisn'])
            ->where('bulan_spp', $validatedData['bulan_spp'])
            ->where('tahun_spp', $validatedData['tahun_spp'])
            ->first();

        // Jika sudah ada dan status LUNAS → STOP
        if ($pembayaran && $pembayaran->status === 'lunas') {
            return back()->withErrors([
                'nisn' => 'Pembayaran untuk bulan tersebut sudah LUNAS.'
            ]);
        }

        // UPLOAD FILE (jika ada)
        if ($request->file('bukti_transfer')) {
            $validatedData['bukti_transfer'] = $request->file('bukti_transfer')->store('bukti-spp', 'public');
        }

        // LOGIKA STATUS OTOMATIS
        $validatedData['status'] = ($request->jumlah_bayar == 0) ? 'lunas' : 'pending';

        /**
         * UPDATE jika data sudah ada (Pending/Ditolak)
         */
        if ($pembayaran) {
            $pembayaran->update([
                'jumlah_bayar'      => $validatedData['jumlah_bayar'],
                'metode_pembayaran' => $validatedData['metode_pembayaran'],
                'bukti_transfer'    => $validatedData['bukti_transfer'] ?? $pembayaran->bukti_transfer,
                'tanggal_bayar'     => $validatedData['tanggal_bayar'],
                'status'            => $validatedData['status'],
            ]);

            return redirect()->route('pembayaran.index')
                ->with('success', 'Pembayaran diperbarui. Status: ' . strtoupper($validatedData['status']));
        }

        /**
         * INSERT Baru jika belum ada
         */
        Pembayaran::create($validatedData);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil disimpan. Status: PENDING (Menunggu Validasi Admin).');
    }

    /**
     * Memvalidasi Pembayaran (Terima/Tolak) oleh Admin.
     */
    public function validasi(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $request->validate([
            'status' => 'required|in:lunas,ditolak',
            'alasan_penolakan' => 'required_if:status,ditolak'
        ]);

        $pembayaran->update([
            'status' => $request->status,
            'alasan_penolakan' => $request->status === 'ditolak' ? $request->alasan_penolakan : null
        ]);

        return redirect()->back()->with('success', 'Status pembayaran diperbarui.');
    }

    /**
     * Menampilkan halaman khusus antrian validasi (Hanya status Pending).
     */
    public function halamanValidasi()
    {
        $pendingPembayarans = Pembayaran::with('murid')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
        return view('pembayaran.validasi', compact('pendingPembayarans'));
    }

    /**
     * Menghapus data pembayaran & File gambarnya.
     */
    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        if ($pembayaran->bukti_transfer) {
            Storage::delete('public/' . $pembayaran->bukti_transfer);
        }

        $pembayaran->delete();

        return redirect()->back()->with('success', 'Data pembayaran dihapus.');
    }

    /**
     * Cetak kwitansi pembayaran dengan Tanda Tangan Gambar (Base64).
     */
    public function kwitansi($id)
    {
        $p = Pembayaran::with('murid')->findOrFail($id);

        $bendahara = User::whereRaw('LOWER(role) = ?', ['bendahara'])->first();
        $bendaharaName = $bendahara->name ?? 'Staff Bendahara';

        // URL LANGSUNG KE FILE (AMAN UNTUK MODAL & MOBILE)
        $signatureUrl = asset('assets/img/ttd.png');

        return view('pembayaran.kwitansi', compact(
            'p',
            'bendaharaName',
            'signatureUrl'
        ));
    }
}
