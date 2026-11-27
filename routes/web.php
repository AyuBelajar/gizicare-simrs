<?php

use App\Livewire\Auth\Login;
use App\Livewire\MasterData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Beranda, login, logout
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => auth()->check()
    ? redirect(Login::halamanAwal(auth()->user()->role))
    : redirect()->route('login'));

Route::get('/login', Login::class)->middleware('guest')->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Halaman aplikasi (wajib login)
|--------------------------------------------------------------------------
| Semua halaman masih memakai view 'placeholder'. Pemilik halaman cukup
| mengganti SATU baris route-nya dengan komponen Livewire buatannya,
| TANPA mengubah ->name(...), contoh:
|   Route::get('/gizi/skrining', \App\Livewire\Gizi\SkriningGizi::class)->name('gizi.skrining');
*/

Route::middleware('auth')->group(function () {

    // Data pasien — Orang 2 (Ahli Gizi, Dapur, Kepala boleh membuka)
    Route::middleware('role:ahli_gizi,petugas_dapur,kepala_instalasi')->group(function () {
       Route::view('/pasien', 'placeholder', ['judul' => 'A3 Daftar Pasien', 'pemilik' => 'Orang 2'])->name('pasien.index');
       Route::view('/pasien/{rawat}', 'placeholder', ['judul' => 'A6 Detail Pasien', 'pemilik' => 'Orang 2'])->name('pasien.show');
    });

    // Dashboard Ahli Gizi — Orang 1
    Route::middleware('role:ahli_gizi')->group(function () {
        Route::view('/gizi/dashboard', 'placeholder', ['judul' => 'A2 Dashboard to-do', 'pemilik' => 'Orang 1'])->name('gizi.dashboard');
    });

    // Asuhan gizi — Orang 3 & 4 (Ahli Gizi mengubah, Kepala melihat)
    Route::middleware('role:ahli_gizi,kepala_instalasi')->group(function () {
        Route::view('/gizi/skrining', 'placeholder', ['judul' => 'A4 Skrining Gizi', 'pemilik' => 'Orang 3'])->name('gizi.skrining');
        Route::view('/gizi/asesmen', 'placeholder', ['judul' => 'A5 Asesmen ADIME', 'pemilik' => 'Orang 3'])->name('gizi.asesmen');
        Route::view('/gizi/monitoring', 'placeholder', ['judul' => 'A8 Monitoring & Edukasi', 'pemilik' => 'Orang 3'])->name('gizi.monitoring');
        Route::view('/gizi/order-diet', 'placeholder', ['judul' => 'A7 Order Diet', 'pemilik' => 'Orang 4'])->name('gizi.order-diet');
    });

    // Produksi dapur — Orang 5 (Dapur mengubah, Ahli Gizi & Kepala melihat)
    Route::middleware('role:petugas_dapur,ahli_gizi,kepala_instalasi')->group(function () {
        Route::view('/dapur/rekap', 'placeholder', ['judul' => 'B1 Rekap Porsi', 'pemilik' => 'Orang 5'])->name('dapur.rekap');
        Route::view('/dapur/sisa', 'placeholder', ['judul' => 'B1 Input Sisa Makanan', 'pemilik' => 'Orang 5'])->name('dapur.sisa');
        Route::view('/dapur/etiket', 'placeholder', ['judul' => 'B2 Cetak Etiket', 'pemilik' => 'Orang 5'])->name('dapur.etiket');
    });

    // Admin — Orang 1, 2, 5
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/master-data', MasterData::class)->name('admin.master');
        Route::get('/admin/akun', \App\Livewire\Admin\KelolaAkun::class)->name('admin.akun');
        Route::view('/admin/audit-log', 'placeholder', ['judul' => 'C3 Audit Log', 'pemilik' => 'Orang 1'])->name('admin.audit');
    });

    // Kepala Instalasi — Orang 4
    Route::middleware('role:kepala_instalasi')->group(function () {
        Route::view('/kepala/dashboard', 'placeholder', ['judul' => 'D1 Dashboard Ringkasan', 'pemilik' => 'Orang 4'])->name('kepala.dashboard');
    });

    // Laporan — Orang 4 (Admin & Kepala)
    Route::middleware('role:admin,kepala_instalasi')->group(function () {
        Route::view('/laporan', 'placeholder', ['judul' => 'D2 Laporan & Ekspor Excel', 'pemilik' => 'Orang 4'])->name('laporan.index');
    });
});