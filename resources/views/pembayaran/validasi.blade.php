@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-hourglass-split"></i> Antrian Validasi Pembayaran
            </h5>
            <span class="badge bg-dark">{{ $pendingPembayarans->count() }} Menunggu</span>
        </div>
        <div class="card-body">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($pendingPembayarans->isEmpty())
                <div class="text-center py-5">
                    <h3 class="text-muted"><i class="bi bi-check-circle-fill text-success display-4"></i></h3>
                    <p class="mt-2 fw-bold text-secondary">Semua Bersih! Tidak ada pembayaran yang perlu divalidasi.</p>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-primary btn-sm">Kembali ke Input</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Tanggal Masuk</th>
                                <th>Siswa</th>
                                <th>Tagihan</th>
                                <th>Nominal</th>
                                <th>Metode & Bukti</th>
                                <th>Aksi Bendahara</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingPembayarans as $p)
                            <tr>
                                {{-- Tanggal --}}
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}<br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }} WIB</small>
                                </td>

                                {{-- Siswa --}}
                                <td>
                                    <div class="fw-bold">{{ $p->murid->nama_lengkap }}</div>
                                    <small class="text-muted">NISN: {{ $p->murid->nisn }}</small>
                                </td>

                                {{-- Periode SPP --}}
                                <td>{{ $p->bulan_spp }} {{ $p->tahun_spp }}</td>

                                {{-- Nominal --}}
                                <td class="fw-bold text-primary">
                                    Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                                </td>

                                {{-- Metode & Bukti --}}
                                <td class="text-center">
                                    <span class="badge bg-info text-dark mb-2">{{ strtoupper($p->metode_pembayaran) }}</span>
                                    <br>
                                    @if($p->bukti_transfer)
                                        <a href="{{ asset('storage/' . $p->bukti_transfer) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i> Cek Bukti
                                        </a>
                                    @else
                                        <span class="text-danger small fst-italic">Tanpa Bukti</span>
                                    @endif
                                </td>

                                {{-- Tombol Aksi --}}
                                <td class="text-center" style="width: 180px;">
                                    <div class="d-flex gap-2 justify-content-center">
                                        
                                        {{-- TOMBOL TERIMA (ACC) --}}
                                        <form action="{{ route('pembayaran.validasi', $p->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="lunas">
                                            <button type="submit" class="btn btn-success btn-sm fw-bold" onclick="return confirm('ACC Pembayaran ini? Uang sudah masuk?')">
                                                <i class="bi bi-check-lg"></i> ACC
                                            </button>
                                        </form>

                                        {{-- TOMBOL TOLAK --}}
                                        <form action="{{ route('pembayaran.validasi', $p->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="ditolak">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tolak pembayaran ini?')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection