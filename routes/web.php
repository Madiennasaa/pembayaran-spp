<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\ProfileController;

// Halaman Utama -> Redirect ke Login (atau dashboard sesuai role jika sudah login)
Route::get('/', function () {
    if (auth()->check()) {
        $role = strtolower(auth()->user()->role ?? '');

        if ($role === 'admin') {
            return redirect('/murid');
        }

        if ($role === 'bendahara') {
            return redirect('/pemasukan/index');
        }

        if ($role === 'wali') {
            return redirect('/pembayaran');
        }
    }

    return redirect()->route('login');
})->name('home');

// Pastikan ini TIDAK ADA di dalam Route::middleware(['auth'])->group(...)

Route::get('/assets/signature/bendahara', function () {
    $path = resource_path('/assets/img/ttd.png');
    if (!file_exists($path)) {
        abort(404);
    }
    // Tambahkan header untuk mencegah isu MIME type/caching
    return response()->file($path, ['Content-Type' => 'image/png']);
})->name('assets.signature.bendahara');

// Grup Middleware 'auth' - Semua pengguna yang sudah login
Route::middleware(['auth'])->group(function () {

    // Profil (Semua Role)
    Route::get('/profile/edit/{id}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');

    // ADMIN - Gunakan 'admin' group (bukan 'role:admin')
    Route::middleware(['admin'])->group(function () {
        Route::get('murid/export', [MuridController::class, 'export'])->name('murid.export');
        Route::resource('murid', MuridController::class);
    });

    // WALI - Gunakan 'wali' group
    Route::middleware(['wali'])->group(function () {
        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::get('/pembayaran/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
        Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->name('pembayaran.show');
        Route::get('/pembayaran/{id}/kwitansi', [PembayaranController::class, 'kwitansi'])->name('pembayaran.kwitansi');
    });

    // BENDAHARA - Gunakan 'bendahara' group
    Route::middleware(['bendahara'])->group(function () {
        // Pemasukan
        Route::get('/pemasukan/export', [PemasukanController::class, 'export'])->name('pemasukan.export');
        Route::get('/pemasukan/print', [PemasukanController::class, 'print'])->name('pemasukan.print');
        Route::get('/pemasukan/{id}/bukti', [PemasukanController::class, 'bukti'])->name('pemasukan.bukti');
        Route::get('/pemasukan/index', [PemasukanController::class, 'index'])->name('pemasukan.index');
        Route::get('/pemasukan/input', [PemasukanController::class, 'create'])->name('pemasukan.create');
        Route::post('/pemasukan/store', [PemasukanController::class, 'store'])->name('pemasukan.store');
        Route::patch('/pemasukan/{id}/validasi', [PemasukanController::class, 'validasi'])->name('pemasukan.validasi');

        // Pembayaran Validasi
        Route::get('/pembayaran/validasi', [PembayaranController::class, 'halamanValidasi'])->name('pembayaran.pending');
        Route::put('/pembayaran/{id}/validasi', [PembayaranController::class, 'validasi'])->name('pembayaran.validasi');

        // Pembayaran Management
        Route::get('/pembayaran/{id}/edit', [PembayaranController::class, 'edit'])->name('pembayaran.edit');
        Route::put('/pembayaran/{id}', [PembayaranController::class, 'update'])->name('pembayaran.update');
        Route::delete('/pembayaran/{id}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
    });
});
