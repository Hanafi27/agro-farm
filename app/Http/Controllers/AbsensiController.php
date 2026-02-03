<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiExport;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Absensi::with(['pegawai.user'])
            ->whereHas('pegawai.user', function($q) use ($user) {
                if ($user->role === 'admin') {
                    $q->where('role', 'pegawai');
                } else {
                    $q->where('id', $user->id);
                }
            });
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }
        
        // Filter by divisi
        if ($request->filled('divisi')) {
            $query->whereHas('pegawai', function($q) use ($request) {
                $q->where('divisi', $request->divisi);
            });
        }
        
        // Filter by tanggal (opsional - jika tidak diisi, tampilkan semua)
        if ($request->filled('tanggal') && $request->tanggal !== '') {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($user->role === 'admin') {
            // Admin hanya melihat absensi pegawai (bukan admin, owner, keuangan)
            $query->whereHas('pegawai.user', function($q) {
                $q->where('role', 'pegawai');
            });
            $absensis = $query->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $pegawai = $user->pegawai;
            if ($pegawai) {
                $query->where('pegawai_id', $pegawai->id);
                $absensis = $query->orderBy('created_at', 'desc')->paginate(10);
            } else {
                $absensis = collect();
            }
        }
        
        // Get statistics
        $stats = $this->getStats($user);
        
        // Get filter options (hanya divisi pegawai)
        $divisiOptions = Pegawai::whereHas('user', function($q) {
            $q->where('role', 'pegawai');
        })->distinct()->pluck('divisi')->filter();
        $statuses = [Absensi::STATUS_HADIR, Absensi::STATUS_IZIN, Absensi::STATUS_ALFA];
        
        return view('absensi.index', compact('absensis', 'stats', 'divisiOptions', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $today = Carbon::today();
        // Hanya ambil pegawai dengan role 'pegawai' (bukan admin, owner, keuangan)
        $pegawais = Pegawai::whereHas('user', function($query) {
            $query->where('role', 'pegawai');
        })->get();
        
        // Get existing absensi for today
        $existingAbsensi = Absensi::whereDate('tanggal', $today)->get();
        $pegawaiIdsWithAbsensi = $existingAbsensi->pluck('pegawai_id')->toArray();
        
        // Create array of pegawai with their absensi status
        $pegawaiList = [];
        foreach ($pegawais as $pegawai) {
            $absensi = $existingAbsensi->where('pegawai_id', $pegawai->id)->first();
            $pegawaiList[] = [
                'pegawai' => $pegawai,
                'absensi' => $absensi,
                'hasAbsensi' => in_array($pegawai->id, $pegawaiIdsWithAbsensi)
            ];
        }
        
        return view('absensi.create', compact('pegawaiList', 'today'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'absensi_data' => 'required|array',
            'absensi_data.*.pegawai_id' => 'required|exists:pegawais,id',
            'absensi_data.*.status' => 'required|in:' . Absensi::STATUS_HADIR . ',' . Absensi::STATUS_IZIN . ',' . Absensi::STATUS_ALFA,
            'absensi_data.*.jam_masuk' => 'nullable|date_format:H:i',
            'absensi_data.*.jam_keluar' => 'nullable|date_format:H:i',
            'absensi_data.*.keterangan' => 'nullable|string',
        ]);

        $tanggal = $request->tanggal;
        $absensiData = $request->absensi_data;
        
        // Delete existing absensi for this date
        Absensi::whereDate('tanggal', $tanggal)->delete();
        
        // Create new absensi records
        foreach ($absensiData as $data) {
            // Validate keterangan for izin and alfa
            if (in_array($data['status'], [Absensi::STATUS_IZIN, Absensi::STATUS_ALFA]) && empty($data['keterangan'])) {
                return back()->withErrors(['keterangan' => 'Keterangan wajib diisi untuk status izin dan alfa']);
            }
            
            Absensi::create([
                'pegawai_id' => $data['pegawai_id'],
                'tanggal' => $tanggal,
                'jam_masuk' => $data['jam_masuk'] ? $tanggal . ' ' . $data['jam_masuk'] : null,
                'jam_keluar' => $data['jam_keluar'] ? $tanggal . ' ' . $data['jam_keluar'] : null,
                'status' => $data['status'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        }

        return redirect()->route('admin.absensi.index')->with('success', 'Absensi berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $absensi = Absensi::with(['pegawai.user'])->findOrFail($id);
        return view('absensi.show', compact('absensi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $absensi = Absensi::with(['pegawai.user'])->findOrFail($id);
        // Hanya ambil pegawai dengan role 'pegawai' (bukan admin, owner, keuangan)
        $pegawais = Pegawai::whereHas('user', function($query) {
            $query->where('role', 'pegawai');
        })->get();
        return view('absensi.edit', compact('absensi', 'pegawais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
            'status' => 'required|in:' . Absensi::STATUS_HADIR . ',' . Absensi::STATUS_IZIN . ',' . Absensi::STATUS_ALFA . ',sakit',
            'keterangan' => 'nullable|string',
        ]);

        $absensi = Absensi::findOrFail($id);
        
        // Check if absensi already exists for this pegawai and date (excluding current record)
        // Only check if pegawai_id or tanggal has changed
        if ($request->pegawai_id != $absensi->pegawai_id || $request->tanggal != $absensi->tanggal->format('Y-m-d')) {
            $existingAbsensi = Absensi::where('pegawai_id', $request->pegawai_id)
                ->whereDate('tanggal', $request->tanggal)
                ->where('id', '!=', $id)
                ->first();
                
            if ($existingAbsensi) {
                return back()->withErrors(['tanggal' => 'Absensi untuk pegawai ini pada tanggal tersebut sudah ada']);
            }
        }

        // Validate keterangan for izin and alfa
        if (in_array($request->status, [Absensi::STATUS_IZIN, Absensi::STATUS_ALFA]) && empty(trim($request->keterangan))) {
            return back()->withErrors(['keterangan' => 'Keterangan wajib diisi untuk status izin dan alfa']);
        }

        $updateData = [
            'pegawai_id' => $request->pegawai_id,
            'tanggal' => $request->tanggal,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ];
        
        // Handle jam_masuk and jam_keluar separately (database columns are TIME type)
        if ($request->jam_masuk) {
            $updateData['jam_masuk'] = $request->jam_masuk;
        } else {
            $updateData['jam_masuk'] = null;
        }
        
        if ($request->jam_keluar) {
            $updateData['jam_keluar'] = $request->jam_keluar;
        } else {
            $updateData['jam_keluar'] = null;
        }
        
        // Update directly using DB to avoid cast issues
        $absensi->update($updateData);

        return redirect()->route('admin.absensi.index')->with('success', 'Absensi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return redirect()->route('admin.absensi.index')->with('success', 'Absensi berhasil dihapus');
    }

    /**
     * Hapus seluruh data absensi
     */
    public function deleteAll()
    {
        try {
            $count = \App\Models\Absensi::count();
            \DB::transaction(function () {
                \App\Models\Absensi::query()->delete();
            });
            return redirect()->route('admin.absensi.index')->with('success', "Berhasil menghapus {$count} data absensi");
        } catch (\Exception $e) {
            return redirect()->route('admin.absensi.index')->with('error', 'Gagal menghapus data absensi: ' . $e->getMessage());
        }
    }

    /**
     * Export to PDF (rekap per pegawai per bulan)
     */
    public function exportPdf(Request $request)
    {
        $absensis = $this->getFilteredData($request);
        
        // Tentukan bulan/tahun rekap
        $bulan = (int) ($request->get('bulan') ?: ($absensis->first()->tanggal?->month ?? now()->month));
        $tahun = (int) ($request->get('tahun') ?: ($absensis->first()->tanggal?->year ?? now()->year));
        
        // Batasi data ke bulan/tahun yang dimaksud (jika belum terfilter)
        $absensis = $absensis->filter(function($a) use ($bulan, $tahun) {
            return ($a->tanggal?->month ?? Carbon::parse($a->tanggal)->month) == $bulan
                && ($a->tanggal?->year ?? Carbon::parse($a->tanggal)->year) == $tahun;
        });
        
        // Bentuk rekap per pegawai
        $rekap = $absensis
            ->groupBy('pegawai_id')
            ->map(function($group) {
                $first = $group->first();
                $hadir = $group->where('status', 'hadir')->count();
                $izin = $group->where('status', 'izin')->count();
                $alfa = $group->where('status', 'alfa')->count();
                return [
                    'pegawai' => $first->pegawai,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'alfa' => $alfa,
                    'total_hari' => $group->count(),
                ];
            })
            ->values();
        
        $periodeLabel = Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y');
        
        $pdf = PDF::loadView('absensi.pdf', [
            'rekap' => $rekap,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'periodeLabel' => $periodeLabel,
        ]);
        
        return $pdf->download('absensi-rekap-' . $periodeLabel . '.pdf');
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new AbsensiExport($request), 'absensi-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Get filtered data for export
     */
    private function getFilteredData(Request $request)
    {
        $query = Absensi::with(['pegawai.user']);
        
        // Hanya ambil absensi pegawai (bukan admin, owner, keuangan)
        $query->whereHas('pegawai.user', function($q) {
            $q->where('role', 'pegawai');
        });
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }
        
        // Filter by divisi
        if ($request->filled('divisi')) {
            $query->whereHas('pegawai', function($q) use ($request) {
                $q->where('divisi', $request->divisi);
            });
        }
        
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        return $query->orderBy('tanggal', 'desc')->get();
    }

    /**
     * Get statistics
     */
    private function getStats($user)
    {
        $stats = [];
        
        if ($user->role === 'admin') {
            // Hanya hitung absensi pegawai (bukan admin, owner, keuangan)
            $baseQuery = Absensi::whereHas('pegawai.user', function($q) {
                $q->where('role', 'pegawai');
            });
            
            $stats['total_absensi'] = $baseQuery->count();
            $stats['total_hadir'] = (clone $baseQuery)->where('status', Absensi::STATUS_HADIR)->count();
            $stats['total_izin'] = (clone $baseQuery)->where('status', Absensi::STATUS_IZIN)->count();
            $stats['total_alfa'] = (clone $baseQuery)->where('status', Absensi::STATUS_ALFA)->count();
            
            // Statistik hari ini
            $todayQuery = Absensi::whereHas('pegawai.user', function($q) {
                $q->where('role', 'pegawai');
            })->whereDate('tanggal', Carbon::today());
            
            $stats['hadir_hari_ini'] = (clone $todayQuery)->where('status', Absensi::STATUS_HADIR)->count();
            $stats['izin_hari_ini'] = (clone $todayQuery)->where('status', Absensi::STATUS_IZIN)->count();
            $stats['alfa_hari_ini'] = (clone $todayQuery)->where('status', Absensi::STATUS_ALFA)->count();
        } else {
            $pegawai = $user->pegawai;
            if ($pegawai) {
                $stats['total_absensi'] = $pegawai->absensis()->count();
                $stats['total_hadir'] = $pegawai->absensis()->where('status', Absensi::STATUS_HADIR)->count();
                $stats['total_izin'] = $pegawai->absensis()->where('status', Absensi::STATUS_IZIN)->count();
                $stats['total_alfa'] = $pegawai->absensis()->where('status', Absensi::STATUS_ALFA)->count();
                
                // Statistik hari ini
                $stats['hadir_hari_ini'] = $pegawai->absensis()->whereDate('tanggal', Carbon::today())->where('status', Absensi::STATUS_HADIR)->count();
                $stats['izin_hari_ini'] = $pegawai->absensis()->whereDate('tanggal', Carbon::today())->where('status', Absensi::STATUS_IZIN)->count();
                $stats['alfa_hari_ini'] = $pegawai->absensis()->whereDate('tanggal', Carbon::today())->where('status', Absensi::STATUS_ALFA)->count();
            }
        }
        
        return $stats;
    }
}
