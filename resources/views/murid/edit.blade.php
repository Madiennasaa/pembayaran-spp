<div class="modal fade" id="modalEdit{{ $murid->id }}" tabindex="1" aria-labelledby="modalEditLabel{{ $murid->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel{{ $murid->id }}">
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Data: {{ $murid->nama_lengkap }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('murid.update', $murid->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-warning border-0 d-flex align-items-center mb-4" style="background: rgba(255, 193, 7, 0.15);">
                        <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                        <small class="text-dark">Pastikan perubahan data sudah benar sebelum menyimpan</small>
                    </div>

                    {{-- Data Murid --}}
                    <h6 class="fw-bold mb-3 text-uppercase" style="color: #FF8C00; font-size: 0.875rem; letter-spacing: 1px;">
                        <i class="bi bi-person-badge me-2"></i>
                        Data Murid
                    </h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                NISN <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="nisn" class="form-control" value="{{ $murid->nisn }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ $murid->nama_lengkap }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                Tempat Lahir <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ $murid->tempat_lahir }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                Tanggal Lahir <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ $murid->tanggal_lahir->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                Jenis Kelamin <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" {{ $murid->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $murid->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Password Baru <small class="text-muted">(Opsional)</small>
                        </label>
                        <input type="text" name="password" class="form-control" placeholder="Masukkan password baru...">
                        <div class="form-text">
                            <i class="bi bi-shield-lock me-1"></i>
                            Biarkan kosong jika tidak ingin mengubah password
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Alamat <span class="text-danger">*</span>
                        </label>
                        <textarea name="alamat" class="form-control" rows="2" required>{{ $murid->alamat }}</textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
