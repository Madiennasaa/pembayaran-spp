<div class="card border-0">
    <div class="card-header bg-white">
        <div class="row g-3 align-items-center">

            <div class="col-12 col-md-3">
                <h5 class="mb-0">
                    <i class="bi bi-people-fill me-2"></i>
                    Data Murid
                </h5>
            </div>

            <div class="col-12 col-md-5">
                <div class="input-group input-group-md">

                    <span class="input-group-text bg-white border-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>

                    <input type="text" class="form-control bg-white ps-2 border-0 shadow-none focus-ring-0"
                        placeholder="Cari Nama atau NISN..." wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="d-flex gap-2 justify-content-md-end">

                    <a href="{{ route('murid.export') }}" class="btn btn-success btn-sm text-white px-3">
                        <i class="bi bi-file-earmark-excel me-1"></i>
                        Laporan
                    </a>

                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal"
                        data-bs-target="#modalTambah">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah
                    </button>

                </div>
            </div>

        </div>
    </div>

    <div class="card-body p-4">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 10%">NISN</th>
                        <th style="width: 25%">Nama Lengkap</th>
                        <th style="width: 5%">JK</th>
                        <th style="width: 15%">TTL</th>
                        <th style="width: 25%">Alamat</th>
                        <th style="width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($murids as $index => $murid)
                        <tr>
                            <td class="text-center fw-semibold">
                                {{ ($murids->currentPage() - 1) * $murids->perPage() + $loop->iteration }}
                            </td>

                            <td class="text-nowrap">
                                <span class="badge bg-light text-dark border">{{ $murid->nisn ?? '-' }}</span>
                            </td>

                            <td class="fw-semibold">{{ $murid->nama_lengkap }}</td>

                            <td class="text-center">
                                <span
                                    class="badge rounded-pill {{ $murid->jenis_kelamin == 'L' ? 'bg-primary' : 'bg-danger' }}"
                                    style="{{ $murid->jenis_kelamin == 'P' ? 'background-color: #d63384 !important;' : '' }}">
                                    {{ $murid->jenis_kelamin }}
                                </span>
                            </td>

                            <td class="text-nowrap small">
                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                {{ $murid->ttl }}
                            </td>

                            <td>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $murid->alamat }}
                                </small>
                            </td>

                            <td class="text-center">
                                <div class="btn-group" role="group">

                                    <button type="button" class="btn btn-sm btn-warning text-white"
                                        data-bs-toggle="modal" data-bs-target="#modalEdit{{ $murid->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#modalHapus{{ $murid->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-1 d-block mb-3" style="opacity: 0.3;"></i>
                                    <p class="mb-0 fw-semibold">Data tidak ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $murids->links() }}
        </div>
    </div>

    @foreach ($murids as $murid)
        @include('murid.edit')
        @include('murid.delete')
    @endforeach
</div>

<script>
    // Fungsi untuk memindahkan modal ke body
    function pindahkanModal() {
        var modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            if (modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }
        });
    }

    document.addEventListener("DOMContentLoaded", pindahkanModal);

    document.addEventListener("livewire:init", () => {
        Livewire.hook('commit', ({
            succeed
        }) => {
            succeed(() => {
                setTimeout(pindahkanModal, 100);
            });
        });
    });

    document.addEventListener("livewire:update", pindahkanModal);
</script>
