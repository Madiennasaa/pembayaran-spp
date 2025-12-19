{{-- resources/views/pemasukan/modal_validasi.blade.php --}}

<div class="modal fade" id="modalValidasi{{ $p->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-shield-check"></i> Validasi Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- A. BUKTI TRANSFER --}}
                <div class="mb-4 text-center">
                    <label class="form-label fw-bold d-block text-muted small text-uppercase mb-2">Bukti
                        Pembayaran</label>

                    @if ($p->bukti_transfer)
                        <div class="border rounded p-1 d-inline-block bg-light">
                            {{-- Pastikan route ini benar untuk menampilkan gambar --}}
                            <img src="{{ route('pemasukan.bukti', $p->id) }}" alt="Bukti Transfer"
                                class="img-fluid rounded" style="max-height: 250px; object-fit: contain;">
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('pemasukan.bukti', $p->id) }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-zoom-in"></i> Lihat Full
                            </a>
                        </div>
                    @else
                        <div class="p-4 bg-light border border-dashed rounded text-muted">
                            <i class="bi bi-image-alt fs-1 d-block mb-2"></i>
                            Tidak ada bukti transfer yang diunggah.
                        </div>
                    @endif
                </div>

                <hr>

                {{-- B. FORM VALIDASI --}}
                <form id="formValidasi{{ $p->id }}" action="{{ route('pembayaran.validasi', $p->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Input Hidden untuk Status (Diisi oleh Javascript) --}}
                    <input type="hidden" name="status" id="inputStatus{{ $p->id }}" value="">

                    <div class="d-grid gap-3">

                        {{-- OPSI 1: TERIMA --}}
                        <button type="button" onclick="submitValidasi('{{ $p->id }}', 'lunas')"
                            class="btn btn-success fw-bold py-2">
                            <i class="bi bi-check-circle"></i> TERIMA (ACC)
                        </button>

                        <div class="d-flex align-items-center">
                            <hr class="flex-grow-1">
                            <span class="mx-2 text-muted small">ATAU TOLAK</span>
                            <hr class="flex-grow-1">
                        </div>

                        {{-- OPSI 2: TOLAK (Dengan Dropdown) --}}
                        <div class="bg-light p-3 rounded border border-danger">
                            <label for="alasan{{ $p->id }}" class="form-label small fw-bold text-danger">
                                Alasan Penolakan (Wajib):
                            </label>

                            <select name="alasan_penolakan" id="alasan{{ $p->id }}"
                                class="form-select form-select-sm mb-2">
                                <option value="">-- Pilih Alasan --</option>
                                <option value="Bukti transfer buram/tidak terbaca">Bukti transfer buram/tidak terbaca
                                </option>
                                <option value="Nominal transfer tidak sesuai">Nominal transfer tidak sesuai</option>
                                <option value="Rekening tujuan salah">Rekening tujuan salah</option>
                                <option value="Tanggal transfer kadaluarsa">Tanggal transfer kadaluarsa</option>
                                <option value="Indikasi bukti transfer palsu">Indikasi bukti transfer palsu</option>
                                <option value="Lainnya">Lainnya (Hubungi Admin)</option>
                            </select>

                            <button type="button" onclick="submitValidasi('{{ $p->id }}', 'ditolak')"
                                class="btn btn-outline-danger w-100 fw-bold btn-sm">
                                <i class="bi bi-x-circle"></i> TOLAK PEMBAYARAN
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT VALIDASI --}}
{{-- Note: Jika file ini di-include dalam loop, pastikan function ini tidak terdeklarasi ganda.
     Idealnya function ini ditaruh di layout utama. Tapi untuk kemudahan copy-paste, saya pakai pengecekan 'typeof'. --}}
<script>
    if (typeof submitValidasi !== 'function') {
        function submitValidasi(id, status) {
            // 1. Isi input hidden status
            document.getElementById('inputStatus' + id).value = status;

            // 2. Jika status DITOLAK, cek dropdown alasan
            if (status === 'ditolak') {
                var alasan = document.getElementById('alasan' + id).value;
                if (alasan === "") {
                    // Alert jika alasan kosong
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Perhatian', 'Harap pilih alasan penolakan terlebih dahulu!', 'warning');
                    } else {
                        alert('Harap pilih alasan penolakan terlebih dahulu!');
                    }
                    return; // Stop proses submit
                }
            }

            // 3. Submit Form
            document.getElementById('formValidasi' + id).submit();
        }
    }
</script>
