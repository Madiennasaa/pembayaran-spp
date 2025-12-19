<div class="modal fade" id="modalHapus{{ $murid->id }}" tabindex="-1" aria-labelledby="modalHapusLabel{{ $murid->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger" id="modalHapusLabel{{ $murid->id }}">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-trash display-1 text-danger" style="opacity: 0.3;"></i>
                </div>
                <p class="mb-2">Apakah Anda yakin ingin menghapus data murid:</p>
                <h6 class="fw-bold text-dark mb-3">{{ $murid->nama_lengkap }}</h6>
                <div class="alert alert-danger border-0 text-start" style="background: rgba(220, 53, 69, 0.1);">
                    <small>
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Peringatan:</strong> Data yang sudah dihapus tidak dapat dikembalikan!
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Batal
                </button>
                <form action="{{ route('murid.destroy', $murid->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>