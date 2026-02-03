<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PendapatanSusuController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\PengajuanDanaController;
use App\Http\Controllers\LaporanRealisasiController;
use App\Http\Controllers\LabaRugiController;

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;

// Test route untuk debugging (tidak dilindungi middleware)
Route::get('/test-pendapatan', function(Request $request) {
    $divisi = $request->get('divisi', 'peternakan');
    $bulan = $request->get('bulan', 7);
    $tahun = $request->get('tahun', 2025);
    
    $pendapatan = \App\Models\PendapatanSusu::whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->where('kategori', $divisi)
        ->get();
    
    return response()->json([
        'success' => true,
        'pendapatan' => $pendapatan,
        'count' => $pendapatan->count(),
        'filters' => [
            'divisi' => $divisi,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]
    ]);
});

Route::get('/test-pengajuan', function(Request $request) {
    $divisi = $request->get('divisi', 'peternakan');
    $bulan = $request->get('bulan', 7);
    $tahun = $request->get('tahun', 2025);
    
    $pengajuan = \App\Models\PengajuanDana::whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->where('divisi', $divisi)
        ->where('status', 'realized')
        ->with('items')
        ->get();
    
    // Flatten all items from all pengajuan into a single array
    $allItems = [];
    foreach($pengajuan as $p) {
        foreach($p->items as $item) {
            $allItems[] = [
                'id' => $item->id,
                'pengajuan_id' => $p->id,
                'nama_kebutuhan' => $item->nama_kebutuhan,
                'jenis_kebutuhan' => $item->jenis_kebutuhan,
                'jumlah' => $item->jumlah,
                'satuan' => $item->satuan,
                'harga_satuan' => $item->harga_satuan,
                'total_harga' => $item->jumlah * $item->harga_satuan,
                'pengajuan_info' => [
                    'id' => $p->id,
                    'minggu' => $p->minggu,
                    'bulan' => $p->bulan,
                    'tahun' => $p->tahun,
                    'status' => $p->status,
                    'divisi' => $p->divisi
                ]
            ];
        }
    }
    
    return response()->json([
        'success' => true,
        'pengajuan' => $pengajuan,
        'items' => $allItems,
        'count' => count($allItems),
        'filters' => [
            'divisi' => $divisi,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]
    ]);
});

Route::get('/', function () {
    return view('welcome');
});

// Dashboard - All authenticated users can access
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Custom Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ========================================
// OWNER ROUTES (Prefix: /owner)
// ========================================
Route::middleware(['auth', 'owner'])->prefix('owner')->name('owner.')->group(function () {
    // Pengajuan Dana - Owner can view and approve/reject
    Route::get('/pengajuan-dana', [PengajuanDanaController::class, 'index'])->name('pengajuan-dana.index');
    Route::get('/pengajuan-dana/history', [PengajuanDanaController::class, 'history'])->name('pengajuan-dana.history');
    Route::get('/pengajuan-dana/{pengajuanDana}', [PengajuanDanaController::class, 'show'])->name('pengajuan-dana.show');
    Route::post('/pengajuan-dana/{pengajuanDana}/approve', [PengajuanDanaController::class, 'approve'])->name('pengajuan-dana.approve');
    Route::post('/pengajuan-dana/{pengajuanDana}/reject', [PengajuanDanaController::class, 'reject'])->name('pengajuan-dana.reject');
    Route::delete('/pengajuan-dana/{pengajuanDana}', [PengajuanDanaController::class, 'destroy'])->name('pengajuan-dana.destroy');
    Route::delete('/pengajuan-dana/history/{pengajuanDana}', [PengajuanDanaController::class, 'deleteHistory'])->name('pengajuan-dana.delete-history');
    Route::post('/pengajuan-dana/history/bulk-delete', [PengajuanDanaController::class, 'bulkDeleteHistory'])->name('pengajuan-dana.bulk-delete-history');
    Route::post('/pengajuan-dana/bulk-delete', [PengajuanDanaController::class, 'bulkDelete'])->name('pengajuan-dana.bulk-delete');
    Route::post('/pengajuan-dana/approve-all-submit', [PengajuanDanaController::class, 'approveAllSubmit'])->name('pengajuan-dana.approve-all-submit');
    
         // Laporan Realisasi - Owner can view and approve
     Route::get('/laporan-realisasi', [LaporanRealisasiController::class, 'index'])->name('laporan-realisasi.index');
     Route::get('/laporan-realisasi/{laporan_realisasi}', [LaporanRealisasiController::class, 'show'])->name('laporan-realisasi.show');
     Route::post('/laporan-realisasi/{laporan_realisasi}/approve', [LaporanRealisasiController::class, 'approve'])->name('laporan-realisasi.approve');
                Route::get('/laporan-realisasi/{laporan_realisasi}/export-pdf', [LaporanRealisasiController::class, 'exportPdf'])->name('laporan-realisasi.export-pdf');
            Route::get('/laporan-realisasi/{laporan_realisasi}/export-excel', [LaporanRealisasiController::class, 'exportExcel'])->name('laporan-realisasi.export-excel');
            Route::get('/laporan-realisasi/{laporan_realisasi}/export-rekapan-pdf', [LaporanRealisasiController::class, 'exportRekapanPdf'])->name('laporan-realisasi.export-rekapan-pdf');
            Route::get('/laporan-realisasi/{laporan_realisasi}/export-rekapan-excel', [LaporanRealisasiController::class, 'exportRekapanExcel'])->name('laporan-realisasi.export-rekapan-excel');
            Route::post('/laporan-realisasi/export-rekapan-bulanan-pdf', [LaporanRealisasiController::class, 'exportRekapanBulananPdf'])->name('laporan-realisasi.export-rekapan-bulanan-pdf');
            Route::post('/laporan-realisasi/export-rekapan-bulanan-excel', [LaporanRealisasiController::class, 'exportRekapanBulananExcel'])->name('laporan-realisasi.export-rekapan-bulanan-excel');
    // Laporan Rekap (UI untuk export rekapan bulanan)
    Route::get('/laporan-rekap', [LaporanRealisasiController::class, 'rekapIndex'])->name('laporan-rekap.index');
    Route::get('/laporan-rekap/{id}', [LaporanRealisasiController::class, 'showRekapan'])->name('laporan-rekap.show');
    Route::get('/laba-rugi', [LabaRugiController::class, 'index'])->name('laba-rugi.index');
    Route::get('/laba-rugi/export-pdf', [LabaRugiController::class, 'exportPdf'])->name('laba-rugi.export-pdf');
    Route::get('/laba-rugi/export-excel', [LabaRugiController::class, 'exportExcel'])->name('laba-rugi.export-excel');
});

// ========================================
// KEUANGAN ROUTES (Prefix: /keuangan)
// ========================================
Route::middleware(['auth', 'keuangan'])->prefix('keuangan')->name('keuangan.')->group(function () {
    // Pengajuan Dana - Keuangan can view and realize
    Route::get('/pengajuan-dana', [PengajuanDanaController::class, 'index'])->name('pengajuan-dana.index');
    Route::get('/pengajuan-dana/history', [PengajuanDanaController::class, 'history'])->name('pengajuan-dana.history');
    Route::get('/pengajuan-dana/{pengajuanDana}', [PengajuanDanaController::class, 'show'])->name('pengajuan-dana.show');
    Route::post('/pengajuan-dana/{pengajuanDana}/realize', [PengajuanDanaController::class, 'realize'])->name('pengajuan-dana.realize');
    Route::delete('/pengajuan-dana/{pengajuanDana}', [PengajuanDanaController::class, 'destroy'])->name('pengajuan-dana.destroy');
    Route::delete('/pengajuan-dana/history/{pengajuanDana}', [PengajuanDanaController::class, 'deleteHistory'])->name('pengajuan-dana.delete-history');
    Route::post('/pengajuan-dana/history/bulk-delete', [PengajuanDanaController::class, 'bulkDeleteHistory'])->name('pengajuan-dana.bulk-delete-history');
    Route::post('/pengajuan-dana/bulk-delete', [PengajuanDanaController::class, 'bulkDelete'])->name('pengajuan-dana.bulk-delete');
    // Route untuk realize all approved dihapus karena keuangan harus upload bukti transfer untuk setiap pencairan
    
         // Laporan Realisasi - Keuangan can only view
     Route::get('/laporan-realisasi', [LaporanRealisasiController::class, 'index'])->name('laporan-realisasi.index');
     Route::get('/laporan-realisasi/{laporan_realisasi}', [LaporanRealisasiController::class, 'show'])->name('laporan-realisasi.show');
                Route::get('/laporan-realisasi/{laporan_realisasi}/export-pdf', [LaporanRealisasiController::class, 'exportPdf'])->name('laporan-realisasi.export-pdf');
            Route::get('/laporan-realisasi/{laporan_realisasi}/export-excel', [LaporanRealisasiController::class, 'exportExcel'])->name('laporan-realisasi.export-excel');
            Route::get('/laporan-realisasi/{laporan_realisasi}/export-rekapan-pdf', [LaporanRealisasiController::class, 'exportRekapanPdf'])->name('laporan-realisasi.export-rekapan-pdf');
            Route::get('/laporan-realisasi/{laporan_realisasi}/export-rekapan-excel', [LaporanRealisasiController::class, 'exportRekapanExcel'])->name('laporan-realisasi.export-rekapan-excel');
            Route::post('/laporan-realisasi/export-rekapan-bulanan-pdf', [LaporanRealisasiController::class, 'exportRekapanBulananPdf'])->name('laporan-realisasi.export-rekapan-bulanan-pdf');
            Route::post('/laporan-realisasi/export-rekapan-bulanan-excel', [LaporanRealisasiController::class, 'exportRekapanBulananExcel'])->name('laporan-realisasi.export-rekapan-bulanan-excel');
    // Laporan Rekap (UI untuk export rekapan bulanan)
    Route::get('/laporan-rekap', [LaporanRealisasiController::class, 'rekapIndex'])->name('laporan-rekap.index');
    Route::get('/laporan-rekap/{id}', [LaporanRealisasiController::class, 'showRekapan'])->name('laporan-rekap.show');
    Route::get('/laba-rugi', [LabaRugiController::class, 'index'])->name('laba-rugi.index');
    Route::get('/laba-rugi/export-pdf', [LabaRugiController::class, 'exportPdf'])->name('laba-rugi.export-pdf');
    Route::get('/laba-rugi/export-excel', [LabaRugiController::class, 'exportExcel'])->name('laba-rugi.export-excel');
});

// ========================================
// ADMIN ROUTES (Prefix: /admin)
// ========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Manajemen Pegawai - Admin only
    Route::resource('pegawai', PegawaiController::class);
    
    // Absensi Pegawai - Admin only
    // Tempatkan delete-all sebelum resource agar tidak tertangkap oleh {absensi}
    Route::get('/absensi/delete-all', function () { return redirect()->route('admin.absensi.index'); });
    Route::post('/absensi/delete-all', [AbsensiController::class, 'deleteAll'])->name('absensi.delete-all.post');
    Route::delete('/absensi/delete-all', [AbsensiController::class, 'deleteAll'])->name('absensi.delete-all');
    Route::resource('absensi', AbsensiController::class);
    Route::get('/absensi/export/pdf', [AbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
    Route::get('/absensi/export/excel', [AbsensiController::class, 'exportExcel'])->name('absensi.export.excel');
    
    // Pendapatan Susu - Admin only
    Route::resource('pendapatan-susu', PendapatanSusuController::class);
    Route::post('/pendapatan-susu/bulk-delete', [PendapatanSusuController::class, 'bulkDelete'])->name('pendapatan-susu.bulk-delete');
    
    // Penggajian - Admin only
    Route::resource('penggajian', PenggajianController::class);
    Route::post('/penggajian/generate', [PenggajianController::class, 'generatePayroll'])->name('penggajian.generate');
    Route::post('/penggajian/generate-range', [PenggajianController::class, 'generatePayrollRange'])->name('penggajian.generate-range');
    Route::get('/penggajian/{penggajian}/export-slip', [PenggajianController::class, 'exportSlip'])->name('penggajian.export-slip');
    Route::get('/api/absensi-count/{pegawaiId}/{bulan}/{tahun}', [PenggajianController::class, 'getAbsensiCount'])->name('api.absensi-count');
    // Delete-all: support POST and DELETE; GET will safely redirect back to index to avoid 404 if accessed directly
    Route::post('/penggajian/delete-all', [PenggajianController::class, 'deleteAll'])->name('penggajian.delete-all.post');
    Route::delete('/penggajian/delete-all', [PenggajianController::class, 'deleteAll'])->name('penggajian.delete-all');
    Route::get('/penggajian/delete-all', function () { return redirect()->route('admin.penggajian.index'); });
    
    // Pengajuan Dana - Admin can manage (create, edit, delete, send)
    Route::get('/pengajuan-dana/history', [PengajuanDanaController::class, 'history'])->name('pengajuan-dana.history');
    Route::resource('pengajuan-dana', PengajuanDanaController::class);
    Route::post('/pengajuan-dana/{pengajuanDana}/send', [PengajuanDanaController::class, 'send'])->name('pengajuan-dana.send');
    Route::post('/pengajuan-dana/send-all-draft', [PengajuanDanaController::class, 'sendAllDraft'])->name('pengajuan-dana.send-all-draft');
    Route::delete('/pengajuan-dana/history/{pengajuanDana}', [PengajuanDanaController::class, 'deleteHistory'])->name('pengajuan-dana.delete-history');
    Route::post('/pengajuan-dana/history/bulk-delete', [PengajuanDanaController::class, 'bulkDeleteHistory'])->name('pengajuan-dana.bulk-delete-history');
    Route::post('/pengajuan-dana/bulk-delete', [PengajuanDanaController::class, 'bulkDelete'])->name('pengajuan-dana.bulk-delete');
    
         // Laporan Realisasi - Admin can manage (CRUD)
     Route::resource('laporan-realisasi', LaporanRealisasiController::class);
     Route::post('/laporan-realisasi/{laporan_realisasi}/update-advanced', [LaporanRealisasiController::class, 'updateAdvanced'])->name('laporan-realisasi.update-advanced');
     // Laporan Rekap (UI untuk export rekapan bulanan)
     Route::get('/laporan-rekap', [LaporanRealisasiController::class, 'rekapIndex'])->name('laporan-rekap.index');
     Route::get('/laporan-rekap/{id}', [LaporanRealisasiController::class, 'showRekapan'])->name('laporan-rekap.show');
     // Bulk delete laporan realisasi
     Route::post('/laporan-realisasi/delete-all', [LaporanRealisasiController::class, 'deleteAll'])->name('laporan-realisasi.delete-all');
     Route::post('/laporan-realisasi/{laporan_realisasi}/send', [LaporanRealisasiController::class, 'send'])->name('laporan-realisasi.send');
     Route::post('/laporan-realisasi/{laporan_realisasi}/approve', [LaporanRealisasiController::class, 'approve'])->name('laporan-realisasi.approve');
     Route::delete('/laporan-realisasi/{laporan_realisasi}/item/{item}', [LaporanRealisasiController::class, 'destroyItem'])->name('laporan-realisasi.destroy-item');
                Route::get('/laporan-realisasi/{laporan_realisasi}/export-pdf', [LaporanRealisasiController::class, 'exportPdf'])->name('laporan-realisasi.export-pdf');
            Route::get('/laporan-realisasi/{laporan_realisasi}/export-excel', [LaporanRealisasiController::class, 'exportExcel'])->name('laporan-realisasi.export-excel');
            Route::get('/laporan-realisasi/{laporan_realisasi}/export-rekapan-pdf', [LaporanRealisasiController::class, 'exportRekapanPdf'])->name('laporan-realisasi.export-rekapan-pdf');
            Route::get('/laporan-realisasi/{laporan_realisasi}/export-rekapan-excel', [LaporanRealisasiController::class, 'exportRekapanExcel'])->name('laporan-realisasi.export-rekapan-excel');
            Route::post('/laporan-realisasi/export-rekapan-bulanan-pdf', [LaporanRealisasiController::class, 'exportRekapanBulananPdf'])->name('laporan-realisasi.export-rekapan-bulanan-pdf');
            Route::post('/laporan-realisasi/export-rekapan-bulanan-excel', [LaporanRealisasiController::class, 'exportRekapanBulananExcel'])->name('laporan-realisasi.export-rekapan-bulanan-excel');
    Route::get('/laba-rugi', [LabaRugiController::class, 'index'])->name('laba-rugi.index');
    Route::get('/laba-rugi/export-pdf', [LabaRugiController::class, 'exportPdf'])->name('laba-rugi.export-pdf');
    Route::get('/laba-rugi/export-excel', [LabaRugiController::class, 'exportExcel'])->name('laba-rugi.export-excel');
});

// API Routes untuk Laporan Realisasi (tidak dilindungi middleware admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/api/pendapatan-susu/get-by-month', [PendapatanSusuController::class, 'getByMonth'])->name('api.pendapatan-susu.get-by-month');
    Route::get('/api/pengajuan-dana/get-by-month', [PengajuanDanaController::class, 'getByMonth'])->name('api.pengajuan-dana.get-by-month');
});



// require __DIR__.'/auth.php'; 