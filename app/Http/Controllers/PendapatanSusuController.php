<?php

namespace App\Http\Controllers;

use App\Models\PendapatanSusu;
use Illuminate\Http\Request;

class PendapatanSusuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            $query = PendapatanSusu::query();
            
            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('kategori', 'like', "%{$search}%")
                      ->orWhere('jenis_produk', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }
            
            // Filter by kategori
            if ($request->filled('kategori')) {
                $query->where('kategori', $request->kategori);
            }
            
            // Filter by jenis_produk
            if ($request->filled('jenis_produk')) {
                $query->where('jenis_produk', $request->jenis_produk);
            }
            
            // Filter by date range
            if ($request->filled('tanggal_awal')) {
                $query->where('tanggal', '>=', $request->tanggal_awal);
            }
            
            if ($request->filled('tanggal_akhir')) {
                $query->where('tanggal', '<=', $request->tanggal_akhir);
            }
            
            $pendapatanSusus = $query->orderBy('tanggal', 'desc')->paginate(10);
            
            // Get unique values for filter dropdowns
            $kategoris = PendapatanSusu::distinct()->pluck('kategori')->filter();
            $jenisProduks = PendapatanSusu::distinct()->pluck('jenis_produk')->filter();
            
            // Calculate statistics
            $totalProduksi = $pendapatanSusus->sum('jumlah_liter');
            $hariIni = PendapatanSusu::whereDate('tanggal', \Carbon\Carbon::today())->sum('jumlah_liter');
            $totalPendapatan = $pendapatanSusus->sum('total_pendapatan');
            $rataRataHari = $pendapatanSusus->count() > 0 ? $pendapatanSusus->sum('jumlah_liter') / $pendapatanSusus->count() : 0;
            
        } else {
            $pendapatanSusus = collect();
            $kategoris = collect();
            $jenisProduks = collect();
            $totalProduksi = 0;
            $hariIni = 0;
            $totalPendapatan = 0;
            $rataRataHari = 0;
        }
        
        return view('pendapatan-susu.index', compact('pendapatanSusus', 'kategoris', 'jenisProduks', 'totalProduksi', 'hariIni', 'totalPendapatan', 'rataRataHari'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pendapatan-susu.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|in:' . PendapatanSusu::KATEGORI_PERKEBUNAN . ',' . PendapatanSusu::KATEGORI_PETERNAKAN,
            'jenis_produk' => 'required|in:' . PendapatanSusu::JENIS_TEH . ',' . PendapatanSusu::JENIS_SUSU_KAMBING . ',' . PendapatanSusu::JENIS_SUSU_SAPI,
            'jumlah_liter' => 'required|numeric|min:0',
            'satuan' => 'required|in:liter,kg',
            'harga_per_liter' => 'required|numeric|min:0',
            'total_pendapatan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Validate jenis_produk based on kategori
        if ($request->kategori === PendapatanSusu::KATEGORI_PERKEBUNAN && $request->jenis_produk !== PendapatanSusu::JENIS_TEH) {
            return back()->withErrors(['jenis_produk' => 'Untuk kategori Perkebunan, jenis produk harus Teh']);
        }
        
        if ($request->kategori === PendapatanSusu::KATEGORI_PETERNAKAN && !in_array($request->jenis_produk, [PendapatanSusu::JENIS_SUSU_KAMBING, PendapatanSusu::JENIS_SUSU_SAPI])) {
            return back()->withErrors(['jenis_produk' => 'Untuk kategori Peternakan, jenis produk harus Susu Kambing atau Susu Sapi']);
        }

        PendapatanSusu::create([
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'jenis_produk' => $request->jenis_produk,
            'jumlah_liter' => round($request->jumlah_liter),
            'satuan' => $request->satuan,
            'harga_per_liter' => round($request->harga_per_liter),
            'total_pendapatan' => round($request->total_pendapatan),
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.pendapatan-susu.index')->with('success', 'Pendapatan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pendapatanSusu = PendapatanSusu::findOrFail($id);
        return view('pendapatan-susu.show', compact('pendapatanSusu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pendapatanSusu = PendapatanSusu::findOrFail($id);
        return view('pendapatan-susu.edit', compact('pendapatanSusu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|in:' . PendapatanSusu::KATEGORI_PERKEBUNAN . ',' . PendapatanSusu::KATEGORI_PETERNAKAN,
            'jenis_produk' => 'required|in:' . PendapatanSusu::JENIS_TEH . ',' . PendapatanSusu::JENIS_SUSU_KAMBING . ',' . PendapatanSusu::JENIS_SUSU_SAPI,
            'jumlah_liter' => 'required|numeric|min:0',
            'satuan' => 'required|in:liter,kg',
            'harga_per_liter' => 'required|numeric|min:0',
            'total_pendapatan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Validate jenis_produk based on kategori
        if ($request->kategori === PendapatanSusu::KATEGORI_PERKEBUNAN && $request->jenis_produk !== PendapatanSusu::JENIS_TEH) {
            return back()->withErrors(['jenis_produk' => 'Untuk kategori Perkebunan, jenis produk harus Teh']);
        }
        
        if ($request->kategori === PendapatanSusu::KATEGORI_PETERNAKAN && !in_array($request->jenis_produk, [PendapatanSusu::JENIS_SUSU_KAMBING, PendapatanSusu::JENIS_SUSU_SAPI])) {
            return back()->withErrors(['jenis_produk' => 'Untuk kategori Peternakan, jenis produk harus Susu Kambing atau Susu Sapi']);
        }

        $pendapatanSusu = PendapatanSusu::findOrFail($id);
        $pendapatanSusu->update([
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'jenis_produk' => $request->jenis_produk,
            'jumlah_liter' => round($request->jumlah_liter),
            'satuan' => $request->satuan,
            'harga_per_liter' => round($request->harga_per_liter),
            'total_pendapatan' => round($request->total_pendapatan),
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.pendapatan-susu.index')->with('success', 'Pendapatan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PendapatanSusu $pendapatanSusu)
    {
        try {
            $pendapatanSusu->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data pendapatan susu berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data pendapatan susu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete pendapatan susu
     */
    public function bulkDelete(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang untuk menghapus pendapatan'], 403);
        }
        
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:pendapatan_susus,id'
        ]);
        
        $pendapatanSusus = PendapatanSusu::whereIn('id', $request->ids)->get();
        $deletedCount = 0;
        
        foreach ($pendapatanSusus as $pendapatanSusu) {
            try {
                $pendapatanSusu->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                // Continue with other pendapatan even if one fails
                continue;
            }
        }
        
        return response()->json([
            'success' => true, 
            'message' => "Berhasil menghapus {$deletedCount} data pendapatan susu"
        ]);
    }

    public function getByMonth(Request $request)
    {
        $user = auth()->user();
        
        // Allow admin, owner, and keuangan to access this data
        if (!$user || !in_array($user->role, ['admin', 'owner', 'keuangan'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }
        
        $request->validate([
            'divisi' => 'required|string',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
        ]);

        $pendapatan = PendapatanSusu::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->where('kategori', $request->divisi)
            ->get();

        return response()->json([
            'success' => true,
            'pendapatan' => $pendapatan,
            'count' => $pendapatan->count()
        ]);
    }
}
