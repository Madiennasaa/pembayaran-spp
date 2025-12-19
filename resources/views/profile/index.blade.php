@extends('layouts.master')

@section('title', 'Edit Profil Siswa dan Orang Tua')

@section('content')
    {{-- Asumsikan Anda memiliki helper atau scope di Controller untuk mendapatkan peran pengguna --}}
    {{-- Anda mungkin perlu mengimpor Auth di file master atau menggunakan sintaks Blade @auth dan @user()->role --}}

    <div class="row justify-content-center my-4">

        <div class="col-lg-9 col-md-10">
            <div class="card shadow-lg p-4 rounded-3">
                <h1 class="h3 font-weight-bold text-gray-800 mb-4 border-bottom pb-3">
                    Edit Profil Siswa dan Orang Tua
                </h1>

                <!-- Notifikasi Status dan Error -->
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <strong class="font-weight-bold">Oops!</strong>
                        <span>Ada masalah dengan input Anda.</span>
                        <ul class="mt-2 list-unstyled">
                            @foreach ($errors->all() as $error)
                                <li><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Update -->
                <form method="POST" action="{{ route('profile.update', $murid->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Tentukan apakah pengguna memiliki peran Admin atau Bendahara untuk mengizinkan input --}}
                    @php
                        $isAdminOrBendahara =
                            Auth::user() &&
                            (strtolower(Auth::user()->role) == 'admin' ||
                                strtolower(Auth::user()->role) == 'bendahara');
                        $isWali = Auth::user() && strtolower(Auth::user()->role) == 'wali';
                    @endphp

                    <!-- Bagian Data Murid -->
                    <div class="p-4 rounded-3 mb-4" style="background-color: #eaf3ff; border: 1px solid #c0d8ff;">
                        <h2 class="h5 text-primary mb-4 pb-2 border-bottom d-flex align-items-center">
                            <i class="bi bi-person-circle me-2"></i>
                            Data Murid
                        </h2>

                        {{-- Input Siswa hanya bisa diubah oleh Admin/Bendahara --}}
                        <input type="hidden" name="update_murid_data" value="1">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nisn" class="form-label text-gray-700">NISN</label>
                                <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $murid->nisn) }}"
                                    readonly class="form-control bg-light @error('nisn') is-invalid @enderror">
                            </div>
                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label text-gray-700">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap"
                                    value="{{ old('nama_lengkap', $murid->nama_lengkap) }}" required
                                    class="form-control @error('nama_lengkap') is-invalid @enderror">
                            </div>
                            <div class="col-md-6">
                                <label for="jenis_kelamin" class="form-label text-gray-700">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="jenis_kelamin" required
                                    class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                    <option value="L"
                                        {{ old('jenis_kelamin', $murid->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki
                                    </option>
                                    <option value="P"
                                        {{ old('jenis_kelamin', $murid->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan
                                    </option>
                                </select>

                            </div>
                            <div class="col-md-6">
                                <label for="tempat_lahir" class="form-label text-gray-700">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir"
                                    value="{{ old('tempat_lahir', $murid->tempat_lahir) }}" required
                                    class="form-control @error('tempat_lahir') is-invalid @enderror">
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_lahir" class="form-label text-gray-700">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                    value="{{ old('tanggal_lahir', $murid->tanggal_lahir) }}" required
                                    class="form-control @error('tanggal_lahir') is-invalid @enderror">
                            </div>
                            <div class="col-12">
                                <label for="alamat" class="form-label text-gray-700">Alamat Murid</label>
                                <textarea name="alamat" id="alamat" rows="3" required
                                    class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $murid->alamat) }}</textarea>
                            </div>
                        </div>

                        <!-- Ganti Password Akun Login (Opsional) -->
                        {{-- Ini boleh diakses oleh semua peran yang bisa mengakses rute profile.edit --}}
                        <div class="col-12 p-3 rounded-3 mt-3"
                            style="background-color: #fffbe6; border: 1px solid #ffe9b4;">
                            <h3 class="h6 text-warning mb-3 border-bottom pb-2 d-flex align-items-center">
                                <i class="bi bi-key-fill me-2"></i>
                                Ganti Password Akun Login (NISN: {{ $murid->nisn }})
                            </h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label text-gray-700">Password Baru</label>
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    <small class="form-text text-muted">Kosongkan jika tidak ingin diubah.</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label text-gray-700">Konfirmasi
                                        Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Bagian Data Orang Tua/Wali -->
                    {{-- Bagian ini dapat diubah oleh Wali, Bendahara, dan Admin --}}
                    <div class="p-4 rounded-3" style="background-color: #e6ffed; border: 1px solid #b3e6c9;">
                        <h2 class="h5 text-success mb-4 pb-2 border-bottom d-flex align-items-center">
                            <i class="bi bi-people-fill me-2"></i>
                            Data Orang Tua / Wali
                        </h2>
                        <input type="hidden" name="update_wali_data" value="1">
                        <div class="row g-3">
                            <!-- Ayah -->
                            <div class="col-12 p-3 rounded-3 border bg-white shadow-sm mb-3">
                                <h3 class="h6 text-secondary mb-3 pb-2 border-bottom">Informasi Ayah</h3>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="nama_ayah" class="form-label text-gray-700">Nama Ayah</label>
                                        <input type="text" name="nama_ayah" id="nama_ayah"
                                            value="{{ old('nama_ayah', $murid->wali->nama_ayah) }}"
                                            class="form-control @error('nama_ayah') is-invalid @enderror">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pekerjaan_ayah" class="form-label text-gray-700">Pekerjaan
                                            Ayah</label>
                                        <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah"
                                            value="{{ old('pekerjaan_ayah', $murid->wali->pekerjaan_ayah) }}"
                                            class="form-control @error('pekerjaan_ayah') is-invalid @enderror">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pendidikan_ayah" class="form-label text-gray-700">Pendidikan
                                            Ayah</label>
                                        <input type="text" name="pendidikan_ayah" id="pendidikan_ayah"
                                            value="{{ old('pendidikan_ayah', $murid->wali->pendidikan_ayah) }}"
                                            class="form-control @error('pendidikan_ayah') is-invalid @enderror">
                                    </div>
                                </div>
                            </div>

                            <!-- Ibu -->
                            <div class="col-12 p-3 rounded-3 border bg-white shadow-sm mb-3">
                                <h3 class="h6 text-secondary mb-3 pb-2 border-bottom">Informasi Ibu</h3>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="nama_ibu" class="form-label text-gray-700">Nama Ibu</label>
                                        <input type="text" name="nama_ibu" id="nama_ibu"
                                            value="{{ old('nama_ibu', $murid->wali->nama_ibu) }}"
                                            class="form-control @error('nama_ibu') is-invalid @enderror">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pekerjaan_ibu" class="form-label text-gray-700">Pekerjaan Ibu</label>
                                        <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu"
                                            value="{{ old('pekerjaan_ibu', $murid->wali->pekerjaan_ibu) }}"
                                            class="form-control @error('pekerjaan_ibu') is-invalid @enderror">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pendidikan_ibu" class="form-label text-gray-700">Pendidikan
                                            Ibu</label>
                                        <input type="text" name="pendidikan_ibu" id="pendidikan_ibu"
                                            value="{{ old('pendidikan_ibu', $murid->wali->pendidikan_ibu) }}"
                                            class="form-control @error('pendidikan_ibu') is-invalid @enderror">
                                    </div>
                                </div>
                            </div>

                            <!-- Kontak Wali -->
                            <div class="col-12 pt-3 border-top mt-3">
                                <h3 class="h6 text-secondary mb-3">Kontak Wali</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="telepon_wali" class="form-label text-gray-700">No. Telepon
                                            Wali</label>
                                        <input type="text" name="telepon_wali" id="telepon_wali"
                                            value="{{ old('telepon_wali', $murid->wali->telepon_wali) }}"
                                            class="form-control @error('telepon_wali') is-invalid @enderror">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="alamat_wali" class="form-label text-gray-700">Alamat Wali (jika
                                            berbeda)</label>
                                        <textarea name="alamat_wali" id="alamat_wali" rows="3"
                                            class="form-control @error('alamat_wali') is-invalid @enderror">{{ old('alamat_wali', $murid->wali->alamat_wali) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-4">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bi bi-save-fill me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
