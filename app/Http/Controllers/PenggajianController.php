<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Penggajian;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenggajianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if (in_array($user->role, ['admin', 'owner', 'keuangan'])) {
            // Hanya tampilkan penggajian untuk pegawai dengan role 'pegawai'
            $penggajians = Penggajian::with(['pegawai.user'])
                ->whereHas('pegawai.user', function($q) {
                    $q->where('role', 'pegawai');
                })
                // Urutan: harian (tanggal desc) lalu bulanan (tahun desc, bulan desc)
                ->orderByRaw("CASE WHEN tipe_periode = 'harian' THEN 0 WHEN tipe_periode = 'rentang' THEN 0 ELSE 1 END")
                ->orderBy('tanggal', 'desc')
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->paginate(10);
        } else {
            $pegawai = $user->pegawai;
            $penggajians = $pegawai ? $pegawai->penggajians()
                ->with(['pegawai.user'])
                ->orderByRaw("CASE WHEN tipe_periode = 'harian' THEN 0 WHEN tipe_periode = 'rentang' THEN 0 ELSE 1 END")
                ->orderBy('tanggal', 'desc')
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->paginate(10) : collect();
        }
        
        return view('penggajian.index', compact('penggajians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Hanya ambil pegawai dengan role 'pegawai' (bukan admin, owner, keuangan)
        $pegawais = Pegawai::whereHas('user', function($query) {
            $query->where('role', 'pegawai');
        })->get();
        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;
        
        return view('penggajian.create', compact('pegawais', 'bulanSekarang', 'tahunSekarang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'tipe_periode' => 'required|in:harian,bulanan',
            'tanggal' => 'required_if:tipe_periode,harian|date|nullable',
            'bulan' => 'required_if:tipe_periode,bulanan|integer|between:1,12|nullable',
            'tahun' => 'required_if:tipe_periode,bulanan|integer|min:2020|nullable',
            'gaji_per_bulan' => 'required|numeric|min:0',
            'gaji_per_minggu' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->tipe_periode === 'harian') {
            return $this->storeHarian($request);
        }

        return $this->storeBulanan($request);
    }

    private function storeHarian(Request $request)
    {
        // Cek duplikasi per pegawai per tanggal
        $exists = Penggajian::where('pegawai_id', $request->pegawai_id)
            ->where('tipe_periode', 'harian')
            ->whereDate('tanggal', $request->tanggal)
            ->exists();
        if ($exists) {
            return back()->withErrors(['tanggal' => 'Penggajian harian untuk tanggal tersebut sudah ada']);
        }

        // Ambil absen hari itu
        $absen = Absensi::where('pegawai_id', $request->pegawai_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        $gajiPerHari = round($request->gaji_per_bulan / 30);
        $totalHadir = ($absen && $absen->status === Absensi::STATUS_HADIR) ? 1 : 0;
        $totalIzin = ($absen && $absen->status === Absensi::STATUS_IZIN) ? 1 : 0;
        $totalAlfa = ($absen && $absen->status === Absensi::STATUS_ALFA) ? 1 : 0;

        // Jika hadir → dibayar 1 hari, izin/alfa → potong 1 hari
        $potongan = round(($totalIzin + $totalAlfa) * $gajiPerHari);
        $totalGaji = round(($totalHadir * $gajiPerHari) - $potongan);
        if ($totalGaji < 0) { $totalGaji = 0; }

        Penggajian::create([
            'pegawai_id' => $request->pegawai_id,
            'tipe_periode' => 'harian',
            'tanggal' => $request->tanggal,
            'bulan' => Carbon::parse($request->tanggal)->month, // Tambahkan bulan
            'tahun' => Carbon::parse($request->tanggal)->year, // Tambahkan tahun
            'gaji_per_bulan' => $request->gaji_per_bulan,
            'gaji_per_minggu' => $request->gaji_per_minggu,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_alfa' => $totalAlfa,
            'potongan' => $potongan,
            'total_gaji' => $totalGaji,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.penggajian.index')->with('success', 'Penggajian harian berhasil dibuat');
    }

    private function storeBulanan(Request $request)
    {
        $pegawai = Pegawai::findOrFail($request->pegawai_id);

        // Check if penggajian already exists for this month
        $existingPenggajian = Penggajian::where('pegawai_id', $request->pegawai_id)
            ->where('tipe_periode', 'bulanan')
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();
            
        if ($existingPenggajian) {
            return back()->withErrors(['bulan' => 'Penggajian untuk bulan ini sudah ada']);
        }

        // Calculate attendance data
        $absensiData = $this->calculateAbsensiData($request->pegawai_id, $request->bulan, $request->tahun);
        
        // Hitung gaji berdasarkan hari hadir saja (sesuai revisi client)
        $gajiPerHari = round($request->gaji_per_bulan / 30); // Gaji per hari
        $totalHariHadir = $absensiData['total_hadir']; // Hanya hari hadir yang dibayar
        
        // Total gaji = jumlah hari hadir × gaji per hari
        $totalGaji = round($totalHariHadir * $gajiPerHari);
        
        // Potongan untuk izin dan alfa (opsional, bisa dihilangkan sesuai kebutuhan)
        $potongan = round(($absensiData['total_izin'] + $absensiData['total_alfa']) * $gajiPerHari);
        $totalGaji = round($totalGaji - $potongan);
        if ($totalGaji < 0) { $totalGaji = 0; }

        Penggajian::create([
            'pegawai_id' => $request->pegawai_id,
            'tipe_periode' => 'bulanan',
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'gaji_per_bulan' => $request->gaji_per_bulan,
            'gaji_per_minggu' => $request->gaji_per_minggu,
            'total_hadir' => $absensiData['total_hadir'],
            'total_izin' => $absensiData['total_izin'],
            'total_alfa' => $absensiData['total_alfa'],
            'potongan' => $potongan,
            'total_gaji' => $totalGaji,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.penggajian.index')->with('success', 'Penggajian bulanan berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penggajian = Penggajian::with(['pegawai.user'])->findOrFail($id);
        return view('penggajian.show', compact('penggajian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penggajian = Penggajian::findOrFail($id);
        // Hanya ambil pegawai dengan role 'pegawai' (bukan admin, owner, keuangan)
        $pegawais = Pegawai::whereHas('user', function($query) {
            $query->where('role', 'pegawai');
        })->get();
        return view('penggajian.edit', compact('penggajian', 'pegawais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'tipe_periode' => 'required|in:harian,bulanan',
            'tanggal' => 'required_if:tipe_periode,harian|date|nullable',
            'bulan' => 'required_if:tipe_periode,bulanan|integer|between:1,12|nullable',
            'tahun' => 'required_if:tipe_periode,bulanan|integer|min:2020|nullable',
            'gaji_per_bulan' => 'required|numeric|min:0',
            'gaji_per_minggu' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $penggajian = Penggajian::findOrFail($id);
        
        if ($request->tipe_periode === 'harian') {
            return $this->updateHarian($request, $penggajian);
        }

        return $this->updateBulanan($request, $penggajian);
    }

    private function updateHarian(Request $request, $penggajian)
    {
        // Cek duplikasi per pegawai per tanggal (kecuali record yang sedang diedit)
        $exists = Penggajian::where('pegawai_id', $request->pegawai_id)
            ->where('tipe_periode', 'harian')
            ->whereDate('tanggal', $request->tanggal)
            ->where('id', '!=', $penggajian->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['tanggal' => 'Penggajian harian untuk tanggal tersebut sudah ada']);
        }

        // Ambil absen hari itu
        $absen = Absensi::where('pegawai_id', $request->pegawai_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        $gajiPerHari = round($request->gaji_per_bulan / 30);
        $totalHadir = ($absen && $absen->status === Absensi::STATUS_HADIR) ? 1 : 0;
        $totalIzin = ($absen && $absen->status === Absensi::STATUS_IZIN) ? 1 : 0;
        $totalAlfa = ($absen && $absen->status === Absensi::STATUS_ALFA) ? 1 : 0;

        // Jika hadir → dibayar 1 hari, izin/alfa → potong 1 hari
        $potongan = round(($totalIzin + $totalAlfa) * $gajiPerHari);
        $totalGaji = round(($totalHadir * $gajiPerHari) - $potongan);
        if ($totalGaji < 0) { $totalGaji = 0; }

        $penggajian->update([
            'pegawai_id' => $request->pegawai_id,
            'tipe_periode' => 'harian',
            'tanggal' => $request->tanggal,
            'bulan' => Carbon::parse($request->tanggal)->month, // Tambahkan bulan
            'tahun' => Carbon::parse($request->tanggal)->year, // Tambahkan tahun
            'gaji_per_bulan' => $request->gaji_per_bulan,
            'gaji_per_minggu' => $request->gaji_per_minggu,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_alfa' => $totalAlfa,
            'potongan' => $potongan,
            'total_gaji' => $totalGaji,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.penggajian.index')->with('success', 'Penggajian harian berhasil diperbarui');
    }

    private function updateBulanan(Request $request, $penggajian)
    {
        // Check if penggajian already exists for this month (excluding current record)
        $existingPenggajian = Penggajian::where('pegawai_id', $request->pegawai_id)
            ->where('tipe_periode', 'bulanan')
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $penggajian->id)
            ->first();
            
        if ($existingPenggajian) {
            return back()->withErrors(['bulan' => 'Penggajian untuk bulan ini sudah ada']);
        }

        // Calculate attendance data
        $absensiData = $this->calculateAbsensiData($request->pegawai_id, $request->bulan, $request->tahun);
        
        // Hitung gaji berdasarkan hari hadir saja (sesuai revisi client)
        $gajiPerHari = round($request->gaji_per_bulan / 30); // Gaji per hari
        $totalHariHadir = $absensiData['total_hadir']; // Hanya hari hadir yang dibayar
        
        // Total gaji = jumlah hari hadir × gaji per hari
        $totalGaji = round($totalHariHadir * $gajiPerHari);
        
        // Potongan untuk izin dan alfa (opsional, bisa dihilangkan sesuai kebutuhan)
        $potongan = round(($absensiData['total_izin'] + $absensiData['total_alfa']) * $gajiPerHari);
        $totalGaji = round($totalGaji - $potongan);
        if ($totalGaji < 0) { $totalGaji = 0; }

        $penggajian->update([
            'pegawai_id' => $request->pegawai_id,
            'tipe_periode' => 'bulanan',
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'gaji_per_bulan' => $request->gaji_per_bulan,
            'gaji_per_minggu' => $request->gaji_per_minggu,
            'total_hadir' => $absensiData['total_hadir'],
            'total_izin' => $absensiData['total_izin'],
            'total_alfa' => $absensiData['total_alfa'],
            'potongan' => $potongan,
            'total_gaji' => $totalGaji,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.penggajian.index')->with('success', 'Penggajian bulanan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penggajian = Penggajian::findOrFail($id);
        $penggajian->delete();

        return redirect()->route('admin.penggajian.index')->with('success', 'Penggajian berhasil dihapus');
    }



    /**
     * Export salary slip to PDF
     */
    public function exportSlip(string $id)
    {
        $penggajian = Penggajian::with(['pegawai.user'])->findOrFail($id);
        
        $pdf = PDF::loadView('penggajian.slip', compact('penggajian'));
        
        return $pdf->download('slip-gaji-' . $penggajian->pegawai->nama . '-' . ($penggajian->bulan ?: '-') . '-' . ($penggajian->tahun ?: '-') . '.pdf');
    }

    /**
     * Get absensi count for API
     */
    public function getAbsensiCount($pegawaiId, $bulan, $tahun)
    {
        $absensiData = $this->calculateAbsensiData($pegawaiId, $bulan, $tahun);
        return response()->json($absensiData);
    }

    /**
     * Generate payroll for all employees for a specific date (harian)
     */
    public function generatePayroll(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = Carbon::parse($request->tanggal)->toDateString();

        $pegawais = Pegawai::with('user')
            ->whereHas('user', fn($q) => $q->where('role', 'pegawai'))
            ->get();

        $generatedCount = 0;
        $errors = [];

        foreach ($pegawais as $pegawai) {
            try {
                // Skip if payroll for this date already exists
                $existingPayroll = Penggajian::where('pegawai_id', $pegawai->id)
                    ->where('tipe_periode', 'harian')
                    ->whereDate('tanggal', $tanggal)
                    ->first();
                if ($existingPayroll) {
                    $errors[] = "Sudah ada penggajian untuk {$pegawai->nama} tanggal {$tanggal}";
                    continue;
                }

                // Get attendance for the date
                $absen = Absensi::where('pegawai_id', $pegawai->id)
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                // Gunakan gaji default jika gaji_pokok kosong
                $gajiPerBulan = (float) ($pegawai->gaji_pokok ?? 5000000);
                $gajiPerMinggu = round($gajiPerBulan / 4);
                $gajiPerHari = round($gajiPerBulan / 30);

                $totalHadir = ($absen && $absen->status === 'hadir') ? 1 : 0;
                $totalIzin = ($absen && $absen->status === 'izin') ? 1 : 0;
                $totalAlfa = ($absen && $absen->status === 'alpha') ? 1 : 0;

                $potongan = round(($totalIzin + $totalAlfa) * $gajiPerHari);
                $totalGaji = round(($totalHadir * $gajiPerHari) - $potongan);
                if ($totalGaji < 0) { $totalGaji = 0; }

                Penggajian::create([
                    'pegawai_id' => $pegawai->id,
                    'tipe_periode' => 'harian',
                    'tanggal' => $tanggal,
                    'bulan' => Carbon::parse($tanggal)->month, // Tambahkan bulan
                    'tahun' => Carbon::parse($tanggal)->year, // Tambahkan tahun
                    'gaji_per_bulan' => $gajiPerBulan,
                    'gaji_per_minggu' => $gajiPerMinggu,
                    'total_hadir' => $totalHadir,
                    'total_izin' => $totalIzin,
                    'total_alfa' => $totalAlfa,
                    'potongan' => $potongan,
                    'total_gaji' => $totalGaji,
                    'keterangan' => 'Generated harian otomatis',
                ]);

                $generatedCount++;
            } catch (\Exception $e) {
                $errors[] = "Error generating payroll untuk {$pegawai->nama}: " . $e->getMessage();
            }
        }

        $message = "Berhasil generate {$generatedCount} penggajian harian untuk tanggal {$tanggal}";
        if (!empty($errors)) {
            $message .= ". Catatan: " . implode(', ', $errors);
        }

        return redirect()->route('admin.penggajian.index')->with('success', $message);
    }

    /**
     * Generate payroll for all employees for a date range (rentang terakumulasi)
     */
    public function generatePayrollRange(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();
        $labelRentang = $tanggalMulai->toDateString() . ' s/d ' . $tanggalSelesai->copy()->startOfDay()->toDateString();

        $pegawais = Pegawai::with('user')
            ->whereHas('user', fn($q) => $q->where('role', 'pegawai'))
            ->get();

        $generatedCount = 0;
        $errors = [];

        Log::info('[Penggajian][Rentang] Mulai generate', [
            'mulai' => $tanggalMulai->toDateString(),
            'selesai' => $tanggalSelesai->toDateString(),
            'pegawai_count' => $pegawais->count(),
        ]);

        foreach ($pegawais as $pegawai) {
            try {
                // Ambil semua absensi pegawai dalam rentang
                $absensis = Absensi::where('pegawai_id', $pegawai->id)
                    ->whereBetween('tanggal', [$tanggalMulai->toDateString(), $tanggalSelesai->toDateString()])
                    ->get();

                Log::info('[Penggajian][Rentang] Cek absensi pegawai', [
                    'pegawai_id' => $pegawai->id,
                    'nama' => $pegawai->nama,
                    'absensi_count' => $absensis->count(),
                ]);

                if ($absensis->isEmpty()) {
                    continue; // tidak ada data dalam rentang
                }

                // Hitung total hadir/izin/alfa dalam rentang
                $totalHadir = $absensis->where('status', 'hadir')->count();
                $totalIzin  = $absensis->where('status', 'izin')->count();
                $totalAlfa  = $absensis->where('status', 'alpha')->count();

                // Gaji dasar
                $gajiPerBulan = (float) ($pegawai->gaji_pokok ?? 5000000);
                $gajiPerMinggu = round($gajiPerBulan / 4);
                $gajiPerHari = round($gajiPerBulan / 30);

                // Potongan dan total gaji terakumulasi
                $potongan = round(($totalIzin + $totalAlfa) * $gajiPerHari);
                $totalGaji = round(($totalHadir * $gajiPerHari) - $potongan);
                if ($totalGaji < 0) { $totalGaji = 0; }

                // Cek jika sudah ada penggajian rentang untuk pegawai dan rentang yang sama -> update
                $existing = Penggajian::where('pegawai_id', $pegawai->id)
                    ->where('tipe_periode', 'rentang')
                    ->where('keterangan', 'LIKE', "%{$labelRentang}%")
                    ->first();

                $payload = [
                    'pegawai_id' => $pegawai->id,
                    'tipe_periode' => 'rentang',
                    'tanggal' => $tanggalMulai->toDateString(), // simpan awal rentang
                    'bulan' => $tanggalMulai->month,
                    'tahun' => $tanggalMulai->year,
                    'gaji_per_bulan' => $gajiPerBulan,
                    'gaji_per_minggu' => $gajiPerMinggu,
                    'total_hadir' => $totalHadir,
                    'total_izin' => $totalIzin,
                    'total_alfa' => $totalAlfa,
                    'potongan' => $potongan,
                    'total_gaji' => $totalGaji,
                    'keterangan' => "Generated rentang tanggal {$labelRentang}",
                ];

                if ($existing) {
                    $existing->update($payload);
                    Log::info('[Penggajian][Rentang] Update entri', ['pegawai_id' => $pegawai->id, 'payload' => $payload]);
                } else {
                    Penggajian::create($payload);
                    Log::info('[Penggajian][Rentang] Create entri', ['pegawai_id' => $pegawai->id, 'payload' => $payload]);
                }

                $generatedCount++;
            } catch (\Exception $e) {
                $errors[] = "Error generate rentang untuk {$pegawai->nama}: " . $e->getMessage();
                Log::error('[Penggajian][Rentang] Error', ['pegawai_id' => $pegawai->id, 'error' => $e->getMessage()]);
            }
        }

        $message = "Berhasil generate {$generatedCount} penggajian rentang untuk periode {$labelRentang}";
        if (!empty($errors)) {
            $message .= ". Catatan: " . implode(', ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " dan " . (count($errors) - 5) . " error lainnya";
            }
        }

        Log::info('[Penggajian][Rentang] Selesai', ['generated' => $generatedCount]);

        return redirect()->route('admin.penggajian.index')->with('success', $message);
    }

    /**
     * Delete all penggajian records
     */
    public function deleteAll()
    {
        try {
            $count = Penggajian::count();
            // Menghindari masalah foreign key: gunakan delete() alih-alih truncate()
            DB::transaction(function () {
                Penggajian::query()->delete();
            });
            
            return redirect()->route('admin.penggajian.index')
                ->with('success', "Berhasil menghapus {$count} data penggajian");
        } catch (\Exception $e) {
            return redirect()->route('admin.penggajian.index')
                ->with('error', 'Gagal menghapus data penggajian: ' . $e->getMessage());
        }
    }

    /**
     * Calculate absensi data for a specific month
     */
    private function calculateAbsensiData($pegawaiId, $bulan, $tahun)
    {
        $absensi = Absensi::where('pegawai_id', $pegawaiId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        return [
            'total_hadir' => $absensi->where('status', 'hadir')->count(),
            'total_izin' => $absensi->where('status', 'izin')->count(),
            'total_alfa' => $absensi->where('status', 'alfa')->count(),
        ];
    }

    /**
     * Calculate potongan based on izin and alfa
     */
    private function calculatePotongan($totalIzin, $totalAlfa, $gajiPerBulan)
    {
        // Asumsi 30 hari kerja per bulan
        $gajiPerHari = $gajiPerBulan / 30;
        
        // Potongan untuk izin dan alfa
        $potonganIzin = $totalIzin * $gajiPerHari;
        $potonganAlfa = $totalAlfa * $gajiPerHari;
        
        return $potonganIzin + $potonganAlfa;
    }
}
