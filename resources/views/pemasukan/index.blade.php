@extends('layouts.master')

@section('title', 'Data Pemasukan SPP')

@section('content')

    {{-- HEADER UTAMA & TOTAL LUNAS BOX --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <h4 class="fw-bold mb-0">Kelola Pemasukan & SPP</h4>
        <div class="bg-success text-white px-3 py-1 rounded fw-bold shadow-sm" style="width: fit-content;">
            Total Lunas: Rp {{ number_format($totalLunas, 0, ',', '.') }}
        </div>
    </div>

    {{-- CARD: FILTER, STATISTIK RINGKAS, DAN TOMBOL AKSI --}}
    <div class="card border-0 shadow-lg mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('pemasukan.index') }}" class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                    <select class="form-select" name="bulan">
                        <option value="">- Semua Bulan -</option>
                        @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                            <option value="{{ $bulan }}" {{ request('bulan') == $bulan ? 'selected' : '' }}>
                                {{ $bulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="tahun">
                        <option value="">- Semua Tahun -</option>
                        @foreach ($listTahun ?? collect() as $t)
                            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>
                                {{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" type="submit">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('pemasukan.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>

                {{-- MODIFIKASI DISINI: Mengarahkan ke route index dengan parameter status=pending --}}
                <div class="col-md-2 text-end">
                    <a href="{{ route('pemasukan.index', ['status' => 'pending']) }}" class="btn btn-warning fw-bold text-dark w-100">
                        Antrian Validasi ({{ $totalPending }})
                    </a>
                </div>
            </form>

            {{-- ROW STATS & EXPORT BUTTONS --}}
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded border">
                        <div class="small text-muted">Total Pemasukan (Lunas)</div>
                        <h5 class="fw-bold mb-0">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h5>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-light rounded border">
                        <div class="small text-muted">Total Transaksi (hasil filter)</div>
                        <h5 class="fw-bold mb-0">{{ $totalTransaksi }}</h5>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-light rounded border">
                        <div class="small text-muted">Menunggu Validasi</div>
                        <h5 class="fw-bold mb-0">{{ $totalPending }}</h5>
                    </div>
                </div>

                <div class="col-md-3 text-end d-flex flex-column gap-2">
                    <a href="{{ route('pemasukan.export', ['search' => request('search'), 'bulan' => request('bulan'), 'tahun' => request('tahun')]) }}"
                        class="btn btn-success fw-bold text-white">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('pemasukan.print', ['search' => request('search'), 'bulan' => request('bulan'), 'tahun' => request('tahun')]) }}"
                        target="_blank" class="btn btn-danger fw-bold text-white">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN TABEL DATA UTAMA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle table-striped mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 20%">Siswa</th>
                            <th style="width: 15%">Periode</th>
                            <th style="width: 15%">Tanggal Bayar</th>
                            <th style="width: 10%">Nominal</th>
                            <th style="width: 10%">Metode</th>
                            <th style="width: 10%">Bukti</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pemasukans as $index => $p)
                            <tr>
                                <td class="text-center">
                                    {{ ($pemasukans->currentPage() - 1) * $pemasukans->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $p->murid->nama_lengkap ?? 'Siswa Terhapus' }}</div>
                                    <small class="text-muted">NISN: {{ $p->nisn }}</small>
                                </td>
                                <td class="text-center">
                                    {{ $p->bulan_spp }} {{ $p->tahun_spp }}
                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d-m-Y') }}
                                </td>
                                <td class="text-end fw-bold text-primary">
                                    Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        {{ strtoupper($p->metode_pembayaran) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($p->bukti_transfer)
                                        <a href="{{ route('pemasukan.bukti', $p->id) }}" target="_blank"
                                            class="btn btn-sm btn-info text-white" title="Lihat Bukti">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($p->status == 'pending')
                                        <span class="badge bg-warning text-dark">PENDING</span>
                                    @elseif($p->status == 'lunas')
                                        <span class="badge bg-success">LUNAS</span>
                                    @else
                                        <span class="badge bg-danger">DITOLAK</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($p->status == 'pending')
                                        <button type="button" class="btn btn-sm btn-primary text-white"
                                            data-bs-toggle="modal" data-bs-target="#modalValidasi{{ $p->id }}"
                                            title="Validasi Pembayaran">
                                            <i class="bi bi-check-square"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @include('pemasukan.validasi', ['p' => $p])
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p class="mb-0">Belum ada data pembayaran ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 d-flex justify-content-center">
                {{ $pemasukans->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL PERINGATAN --}}
    <div class="modal fade" id="modalPeringatan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Perhatian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p id="peringatanText">Harap pilih alasan penolakan terlebih dahulu!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning w-100" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI --}}
    <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-question-circle"></i> Konfirmasi Aksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3">Apakah Anda yakin ingin mengubah status menjadi <strong id="statusKonfirmasi"></strong>?</p>
                </div>
                <div class="modal-footer d-block">
                    <button type="button" id="btnSubmitKonfirmasi" class="btn btn-primary w-100 fw-bold mb-2">Ya, Lanjutkan</button>
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let formToSubmit = null;
        let finalStatus = '';

        document.addEventListener("DOMContentLoaded", function() {
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                document.body.appendChild(modal);
            });

            const modalKonfirmasiEl = document.getElementById('modalKonfirmasi');
            const modalKonfirmasi = new bootstrap.Modal(modalKonfirmasiEl);

            document.getElementById('btnSubmitKonfirmasi').addEventListener('click', function() {
                modalKonfirmasi.hide();
                if (formToSubmit) {
                    let id = formToSubmit.id.replace('formValidasi', '');
                    if (document.getElementById('inputStatus' + id).value === '') {
                        document.getElementById('inputStatus' + id).value = finalStatus;
                    }
                    formToSubmit.submit();
                }
            });
        });

        function submitValidasi(id, status) {
            const form = document.getElementById('formValidasi' + id);
            const inputStatus = document.getElementById('inputStatus' + id);
            const selectAlasan = document.getElementById('alasan' + id);
            const modalPeringatan = new bootstrap.Modal(document.getElementById('modalPeringatan'));
            const modalKonfirmasi = new bootstrap.Modal(document.getElementById('modalKonfirmasi'));

            inputStatus.value = status;
            finalStatus = status;

            if (status === 'ditolak') {
                if (selectAlasan.value === "") {
                    document.getElementById('peringatanText').innerText = "Harap pilih ALASAN PENOLAKAN terlebih dahulu.";
                    modalPeringatan.show();
                    return;
                }
            }

            formToSubmit = form;
            document.getElementById('statusKonfirmasi').innerText = status.toUpperCase();
            modalKonfirmasi.show();
        }
    </script>
@endsection
