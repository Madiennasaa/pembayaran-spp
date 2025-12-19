@extends('layouts.master')

@section('title', 'Input Pembayaran SPP')

@section('content')
    @php
        $userRole = Auth::user()->role ?? 'guest';
        $isWali = $userRole == 'wali';
        // Ambil data bendahara (opsional, sesuaikan dengan logic Anda)
        $bendahara = \App\Models\User::whereRaw('LOWER(role) = ?', ['bendahara'])->first();
        $bendaharaName = $bendahara->name ?? 'Bendahara Sekolah';

        // Cek path tanda tangan
        $hasSignature = file_exists(resource_path('views/assets/img/ttd.png'));
        // Note: Logic file_exists view path agak tricky di blade,
        // lebih baik cek via controller atau public_path jika file di folder public.

        // Persiapan data untuk Javascript
        $historyForJs =
            $pembayarans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pembayarans->items() : $pembayarans;
    @endphp

    <div class="container py-2">
        <div class="row justify-content-center">

            {{-- KOLOM KIRI: FORM INPUT --}}
            <div class="col-md-5 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="bi bi-wallet2"></i> Input Pembayaran
                        </h5>
                    </div>
                    <div class="card-body">

                        {{-- Form Mulai --}}
                        <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data"
                            id="formPembayaran">
                            @csrf

                            {{-- INPUT MURID --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Murid</label>
                                @if ($isWali && $murids->count() > 0)
                                    <input type="text" class="form-control"
                                        value="{{ $murids->first()->nama_lengkap }} ({{ $murids->first()->nisn }})"
                                        readonly>
                                    {{-- Hidden Input untuk JS Wali --}}
                                    <input type="hidden" id="inputNisnWali" name="nisn"
                                        value="{{ $murids->first()->nisn }}">
                                    <span id="waliData" data-tagihan="{{ $murids->first()->tagihan_default }}"
                                        data-bulan="{{ $murids->first()->next_bulan }}"
                                        data-tahun="{{ $murids->first()->next_tahun }}"></span>
                                @else
                                    <select name="nisn" id="selectMurid"
                                        class="form-select @error('nisn') is-invalid @enderror" required>
                                        <option value="" data-tagihan="">-- Pilih Murid --</option>
                                        @foreach ($murids as $m)
                                            <option value="{{ $m->nisn }}" data-tagihan="{{ $m->tagihan_default }}"
                                                data-bulan="{{ $m->next_bulan }}" data-tahun="{{ $m->next_tahun }}">
                                                {{ $m->nama_lengkap }} ({{ $m->nisn }})
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('nisn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- INPUT BULAN & TAHUN --}}
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">Bulan</label>
                                    <select name="bulan_spp" id="selectBulan" class="form-select" required>
                                        @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                                            <option value="{{ $bulan }}">{{ $bulan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">Tahun</label>
                                    <input type="number" name="tahun_spp" id="inputTahun" class="form-control"
                                        value="{{ date('Y') }}" required>
                                </div>
                            </div>

                            {{-- INPUT JUMLAH --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jumlah Bayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="jumlah_bayar" id="inputJumlah" class="form-control bg-light"
                                        placeholder="0" readonly required>
                                </div>
                                <small id="statusInfo" class="text-success fw-bold d-none">
                                    <i class="bi bi-check-circle"></i> Bulan ini sudah LUNAS/PENDING
                                </small>
                            </div>

                            {{-- INPUT METODE --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Metode</label>
                                <select name="metode_pembayaran" id="selectMetode" class="form-select" required>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>

                            {{-- INFO PEMBAYARAN --}}
                            <div id="infoPembayaran" class="mb-3 d-none">
                                <div id="infoTransfer" class="alert alert-info border-info d-none">
                                    <h6 class="fw-bold"><i class="bi bi-bank"></i> Rekening Sekolah:</h6>
                                    <ul class="mb-0 small ps-3">
                                        <li><strong>BCA:</strong> 123-456-7890 (Sekolah)</li>
                                        <li><strong>BRI:</strong> 0000-1111-2222 (Yayasan)</li>
                                    </ul>
                                </div>

                                <div id="infoQris" class="text-center border p-3 rounded bg-light d-none">
                                    <h6 class="fw-bold mb-2">Scan QRIS:</h6>
                                    {{-- Pastikan gambar QRIS ada --}}
                                    <img src="{{ asset('assets/img/TK DHARMA WANITA.png') }}" alt="QRIS Code" class="img-fluid"
                                        style="max-width: 200px;">
                                </div>
                            </div>

                            {{-- UPLOAD BUKTI --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Bukti</label>
                                <input type="file" name="bukti_transfer" class="form-control">
                                <small class="text-muted">*Wajib diunggah.</small>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                <i class="bi bi-save"></i> SIMPAN PEMBAYARAN
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: TABEL HISTORY --}}
            <div class="col-md-7">
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="bi bi-clock-history"></i> Riwayat Pembayaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Murid</th>
                                        <th>Periode</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pembayarans as $p)
                                        <tr>
                                            <td>{{ $p->murid->nama_lengkap }}</td>
                                            <td>{{ $p->bulan_spp }} {{ $p->tahun_spp }}</td>
                                            <td>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                @if ($p->status == 'lunas')
                                                    <span class="badge bg-success">LUNAS</span>
                                                @elseif($p->status == 'pending')
                                                    <span class="badge bg-warning text-dark">PENDING</span>
                                                @else
                                                    <span class="badge bg-danger">DITOLAK</span>
                                                @endif
                                            </td>
                                            <td class="text-center">

                                                {{-- TOMBOL LUNAS (KWITANSI) --}}
                                                @if ($p->status == 'lunas')
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalKwitansi{{ $p->id }}"
                                                        title="Lihat Kwitansi">
                                                        <i class="bi bi-printer"></i>
                                                    </button>

                                                    {{-- TOMBOL DITOLAK (LIHAT ALASAN) --}}
                                                @elseif($p->status == 'ditolak')
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalTolak{{ $p->id }}"
                                                        title="Lihat Alasan">
                                                        <i class="bi bi-info-circle"></i> Alasan
                                                    </button>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="bi bi-inbox display-6 d-block mb-2 text-muted"></i>
                                                <p class="text-muted mb-0">Belum ada riwayat pembayaran</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $pembayarans->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL LOOPING --}}
    @foreach ($pembayarans as $p)
        {{-- A. MODAL KWITANSI (Jika Lunas) --}}
        @if ($p->status == 'lunas')
            <div class="modal fade" id="modalKwitansi{{ $p->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-receipt"></i> Kwitansi Pembayaran
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            {{-- Desain Kwitansi Sederhana --}}
                            <div class="border p-4 rounded bg-light">
                                <div class="text-center mb-3 border-bottom pb-2">
                                    <h4 class="fw-bold">KWITANSI PEMBAYARAN SPP</h4>
                                    <p class="mb-0 text-muted">No: KW-{{ str_pad($p->id, 6, '0', STR_PAD_LEFT) }}</p>
                                </div>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%">Telah terima dari</td>
                                        <td width="2%">:</td>
                                        <td class="fw-bold">{{ $p->murid->nama_lengkap }}</td>
                                    </tr>
                                    <tr>
                                        <td>Untuk Pembayaran</td>
                                        <td>:</td>
                                        <td>SPP Bulan {{ $p->bulan_spp }} {{ $p->tahun_spp }}</td>
                                    </tr>
                                    <tr>
                                        <td>Sejumlah</td>
                                        <td>:</td>
                                        <td class="fw-bold fs-5">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Bayar</td>
                                        <td>:</td>
                                        <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d F Y') }}</td>
                                    </tr>
                                </table>
                                <div class="text-end mt-4">
                                    <p class="mb-1">Bendahara,</p>
                                    {{-- Tanda Tangan Otomatis --}}
                                    <div style="height: 80px;" class="mb-2">
                                        <img src="{{ asset('assets/img/ttd.png') }}" alt="Tanda Tangan"
                                            style="height: 80px; object-fit: contain;">
                                    </div>
                                    <p class="fw-bold mb-0 text-decoration-underline">{{ $bendaharaName }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <a href="{{ route('pembayaran.kwitansi', $p->id) }}" target="_blank"
                                class="btn btn-primary">
                                <i class="bi bi-printer"></i> Cetak PDF
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- B. MODAL ALASAN PENOLAKAN (Jika Ditolak) --}}
        @if ($p->status == 'ditolak')
            <div class="modal fade" id="modalTolak{{ $p->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-exclamation-triangle-fill"></i> Pembayaran Ditolak
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <div class="text-danger display-1 mb-2">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                                <h5 class="fw-bold">Maaf, Pembayaran Ditolak</h5>
                                <p class="text-muted">Silakan perbaiki data dan lakukan upload ulang.</p>
                            </div>

                            <div class="alert alert-light border border-danger rounded-3">
                                <strong class="text-danger d-block mb-1 text-uppercase small">Alasan Penolakan:</strong>
                                {{-- Mengambil data alasan_penolakan dari database --}}
                                <p class="mb-0 fw-bold text-dark fs-5">
                                    {{ $p->alasan_penolakan ?? 'Alasan tidak spesifik.' }}
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            {{-- Form Hapus untuk Upload Ulang --}}
                            <form action="{{ route('pembayaran.destroy', $p->id) }}" method="POST" class="w-100">
                                @csrf
                                @method('DELETE')
                                {{-- <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Hapus data ini agar bisa upload ulang?')">
                                    <i class="bi bi-trash"></i> Hapus & Upload Ulang
                                </button> --}}
                            </form>
                            <button type="button" class="btn btn-secondary w-100 mt-2"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. Flash Messages ---
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            @if ($errors->any())
                let errorHtml = '<ul style="text-align: left;">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li>{{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    html: errorHtml,
                });
            @endif


            // --- 2. Logic Hitung Tagihan & Auto Fill ---
            const historyPembayaran = @json($historyForJs);
            const selectMurid = document.getElementById('selectMurid');
            const waliDataEl = document.getElementById('waliData');
            const inputNisnWali = document.getElementById('inputNisnWali');
            const selectBulan = document.getElementById('selectBulan');
            const inputTahun = document.getElementById('inputTahun');
            const inputJumlah = document.getElementById('inputJumlah');
            const statusInfo = document.getElementById('statusInfo');
            const selectMetode = document.getElementById('selectMetode');

            function hitungTagihan() {
                let defaultTagihan = 0;
                let selectedNisn = '';

                if (selectMurid) {
                    const selectedOption = selectMurid.options[selectMurid.selectedIndex];
                    defaultTagihan = selectedOption.getAttribute('data-tagihan');
                    selectedNisn = selectedOption.value;
                } else if (waliDataEl && inputNisnWali) {
                    defaultTagihan = waliDataEl.getAttribute('data-tagihan');
                    selectedNisn = inputNisnWali.value;
                }

                if (defaultTagihan && selectedNisn && selectBulan.value && inputTahun.value) {
                    const alreadyPaid = historyPembayaran.some(item => {
                        return item.nisn == selectedNisn &&
                            item.bulan_spp == selectBulan.value &&
                            item.tahun_spp == inputTahun.value &&
                            (item.status == 'lunas' || item.status == 'pending');
                    });

                    if (alreadyPaid) {
                        inputJumlah.value = 0;
                        inputJumlah.classList.remove('bg-light');
                        statusInfo.classList.remove('d-none');
                    } else {
                        inputJumlah.value = defaultTagihan;
                        inputJumlah.classList.remove('bg-success', 'text-white');
                        inputJumlah.classList.add('bg-light');
                        statusInfo.classList.add('d-none');
                    }
                } else {
                    inputJumlah.value = "";
                    statusInfo.classList.add('d-none');
                }
            }

            function autoSelectBulan() {
                if (selectMurid) {
                    const selectedOption = selectMurid.options[selectMurid.selectedIndex];
                    const nextBulan = selectedOption.getAttribute('data-bulan');
                    const nextTahun = selectedOption.getAttribute('data-tahun');
                    if (nextBulan) selectBulan.value = nextBulan;
                    if (nextTahun) inputTahun.value = nextTahun;
                } else if (waliDataEl) {
                    const nextBulan = waliDataEl.getAttribute('data-bulan');
                    const nextTahun = waliDataEl.getAttribute('data-tahun');
                    if (nextBulan) selectBulan.value = nextBulan;
                    if (nextTahun) inputTahun.value = nextTahun;
                }
                hitungTagihan();
            }

            if (selectMurid) {
                selectMurid.addEventListener('change', autoSelectBulan);
            } else {
                autoSelectBulan();
            }

            selectBulan.addEventListener('change', hitungTagihan);
            inputTahun.addEventListener('input', hitungTagihan);

            // Trigger awal
            hitungTagihan();

            // UI Metode Pembayaran
            const infoPembayaran = document.getElementById('infoPembayaran');
            const infoTransfer = document.getElementById('infoTransfer');
            const infoQris = document.getElementById('infoQris');

            function updateMetodeDisplay() {
                const metode = selectMetode.value;
                infoPembayaran.classList.add('d-none');
                infoTransfer.classList.add('d-none');
                infoQris.classList.add('d-none');

                if (metode === 'transfer') {
                    infoPembayaran.classList.remove('d-none');
                    infoTransfer.classList.remove('d-none');
                } else if (metode === 'qris') {
                    infoPembayaran.classList.remove('d-none');
                    infoQris.classList.remove('d-none');
                }
            }
            selectMetode.addEventListener('change', updateMetodeDisplay);
            updateMetodeDisplay();
        });
    </script>
@endsection
