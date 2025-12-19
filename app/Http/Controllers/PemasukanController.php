<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Murid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PemasukanExport;

class PemasukanController extends Controller
{
    /**
     * Menampilkan Dashboard Sekaligus Tabel Data
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with('murid');

        // Filter Pencarian (NISN, Tahun, Nama)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                    ->orWhere('tahun_spp', 'like', "%{$search}%")
                    ->orWhereHas('murid', function ($subQ) use ($search) {
                        $subQ->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        // Filter berdasarkan Bulan
        if ($request->filled('bulan')) {
            $query->where('bulan_spp', $request->bulan);
        }

        // Filter berdasarkan Tahun
        if ($request->filled('tahun')) {
            $query->where('tahun_spp', $request->tahun);
        }

        /**
         * TAMBAHAN: Filter berdasarkan Status
         * Digunakan ketika tombol "Antrian Validasi" diklik
         */
        if ($request->get('status') == 'pending') {
            $query->where('status', 'pending');
        }

        // --- PENGAMBILAN DATA STATISTIK ---
        // Total Transaksi dihitung berdasarkan hasil filter tabel saat ini
        $totalTransaksi = (clone $query)->count();

        // Total Lunas & Pending diambil secara global (atau bisa disesuaikan filter jika mau)
        $totalLunas = Pembayaran::where('status', 'lunas')->sum('jumlah_bayar');
        $totalPending = Pembayaran::where('status', 'pending')->count();
        $totalDitolak = Pembayaran::where('status', 'ditolak')->count();

        // Eksekusi Query dengan Pagination
        $pemasukans = $query->latest()->paginate(10)->appends($request->query());

        // List tahun untuk dropdown filter
        $listTahun = Pembayaran::select('tahun_spp')
            ->distinct()
            ->orderBy('tahun_spp', 'desc')
            ->pluck('tahun_spp');

        return view('pemasukan.index', compact(
            'pemasukans',
            'totalTransaksi',
            'totalLunas',
            'totalPending',
            'totalDitolak',
            'listTahun'
        ));
    }

    /**
     * Menampilkan Form Input Pembayaran
     */
    public function create()
    {
        $murids = Murid::all()->map(function ($murid) {
            $bulanIni = Carbon::now()->translatedFormat('F');
            $tahunIni = Carbon::now()->year;

            $sudahBayar = Pembayaran::where('nisn', $murid->nisn)
                ->where('bulan_spp', $bulanIni)
                ->where('tahun_spp', $tahunIni)
                ->exists();

            $murid->status_bulan_ini = $sudahBayar;
            $murid->tagihan_default  = 150000;
            $murid->next_bulan       = $bulanIni;
            $murid->next_tahun       = $tahunIni;

            return $murid;
        });

        $pembayarans = Pembayaran::with('murid')->latest()->paginate(10);

        return view('bendahara.pemasukan_create', compact('murids', 'pembayarans'));
    }

    /**
     * Menyimpan Data Pembayaran (Input Manual oleh Bendahara)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn'              => 'required|exists:murid,nisn',
            'bulan_spp'         => 'required',
            'tahun_spp'         => 'required',
            'jumlah_bayar'      => 'required|numeric',
            'metode_pembayaran' => 'required',
            'tanggal_bayar'     => 'required|date',
            'bukti_transfer'    => 'nullable|image|max:2048',
        ]);

        // Cek Pembayaran Ganda
        $cekDouble = Pembayaran::where('nisn', $request->nisn)
            ->where('bulan_spp', $request->bulan_spp)
            ->where('tahun_spp', $request->tahun_spp)
            ->exists();

        if ($cekDouble) {
            return back()->with('error', 'Siswa ini sudah membayar bulan tersebut!')->withInput();
        }

        // Upload Bukti jika ada
        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiPath = $request->file('bukti_transfer')->store('bukti_bayar', 'public');
        }

        // Simpan dengan status langsung Lunas karena diinput oleh Bendahara
        Pembayaran::create([
            'nisn'              => $request->nisn,
            'bulan_spp'         => $request->bulan_spp,
            'tahun_spp'         => $request->tahun_spp,
            'tanggal_bayar'     => $request->tanggal_bayar,
            'jumlah_bayar'      => $request->jumlah_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'bukti_transfer'    => $buktiPath,
            'status'            => 'lunas',
        ]);

        return redirect()->route('pemasukan.create')->with('success', 'Pembayaran Berhasil Disimpan!');
    }

    /**
     * Validasi Pembayaran dari Siswa (Pending -> Lunas/Ditolak)
     */
    public function validasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:lunas,ditolak',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);

        if ($pembayaran->status !== 'pending') {
            return back()->with('error', 'Pembayaran sudah divalidasi!');
        }

        $pembayaran->update([
            'status' => $request->status
        ]);

        $pesan = $request->status == 'lunas'
            ? 'Pembayaran berhasil di-ACC (Lunas)!'
            : 'Pembayaran telah ditolak.';

        return redirect()->back()->with('success', $pesan);
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $fileName = 'data_pemasukan_spp_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new PemasukanExport($search, $bulan, $tahun), $fileName);
    }

    public function print(Request $request)
    {
        $query = Pembayaran::with('murid')->where('status', 'lunas');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                    ->orWhere('tahun_spp', 'like', "%{$search}%")
                    ->orWhereHas('murid', function ($subQ) use ($search) {
                        $subQ->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('bulan')) {
            $query->where('bulan_spp', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_spp', $request->tahun);
        }

        $pemasukans = $query->latest()->get();
        $totalTransaksi = $pemasukans->count();
        $totalLunas = $pemasukans->sum('jumlah_bayar');

        return view('pemasukan.print', compact('pemasukans', 'totalTransaksi', 'totalLunas'));
    }

    public function bukti($id)
    {
        $p = Pembayaran::findOrFail($id);
        if (!$p->bukti_transfer) {
            abort(404);
        }

        $fullPath = storage_path('app/public/' . $p->bukti_transfer);
        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    }
}
