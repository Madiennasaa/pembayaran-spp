<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content" style="max-height: 90vh;">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Tambah Murid Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('murid.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- Alert Info --}}
                    <div class="alert alert-info border-0 d-flex align-items-center mb-4" style="background: rgba(255, 230, 163, 0.3); color: #FF8C00;">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <small>Lengkapi data murid dengan benar dan teliti</small>
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
                            <input type="number" name="nisn" class="form-control" placeholder="Masukkan NISN" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                Tempat Lahir <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="tempat_lahir" class="form-control" placeholder="Kota lahir" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                Tanggal Lahir <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_lahir" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                Jenis Kelamin <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">Pilih...</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Password <small class="text-muted">(Opsional)</small>
                        </label>
                        <input type="text" name="password" class="form-control" placeholder="Default: Mengikuti NISN">
                        <div class="form-text">
                            <i class="bi bi-shield-check me-1"></i>
                            Biarkan kosong jika ingin password otomatis sama dengan NISN
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Alamat <span class="text-danger">*</span>
                        </label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Masukkan alamat lengkap" required></textarea>
                    </div>
 
                    {{-- Data Orang Tua --}}
                    <div class="border-top pt-4 mt-2"></div>
                    <h6 class="fw-bold mb-3 text-uppercase" style="color: #FF8C00; font-size: 0.875rem; letter-spacing: 1px;">
                        <i class="bi bi-people me-2"></i>
                        Data Orang Tua / Wali
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="form-control" placeholder="Nama ayah kandung">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" class="form-control" placeholder="Pekerjaan ayah">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Pendidikan Ayah</label>
                            <input type="text" name="pendidikan_ayah" class="form-control" placeholder="Pendidikan terakhir">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="form-control" placeholder="Nama ibu kandung">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" class="form-control" placeholder="Pekerjaan ibu">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Pendidikan Ibu</label>
                            <input type="text" name="pendidikan_ibu" class="form-control" placeholder="Pendidikan terakhir">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Telepon Wali</label>
                            <input type="text" name="telepon_wali" class="form-control" placeholder="No. HP orang tua">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Alamat Wali</label>
                            <input type="text" name="alamat_wali" class="form-control" placeholder="Alamat orang tua">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Tutup
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>