<?php

namespace App\Http\Controllers;

use App\Models\LaporanRealisasi;
use App\Models\LaporanRealisasiItem;
use App\Models\LaporanRealisasiItemAttachment;
use App\Models\PengajuanDana;
use App\Models\RekapanLaporan;
use App\Exports\RekapanLaporanExport;
use App\Exports\RekapanLaporanBulananExport;
use App\Events\LaporanRealisasiUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanRealisasiExport;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;

class LaporanRealisasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $laporanRealisasis = LaporanRealisasi::with(['submittedBy', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('laporan-realisasi.index', compact('laporanRealisasis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Get current month and year
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Get current week of month
        $currentWeek = ceil(now()->day / 7);
        
        // Get approved and realized pengajuan dana separated by divisi
        $peternakanPengajuan = PengajuanDana::with(['items', 'submittedBy'])
            ->whereIn('status', [PengajuanDana::STATUS_APPROVED, PengajuanDana::STATUS_REALIZED])
            ->where('divisi', 'peternakan')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $perkebunanPengajuan = PengajuanDana::with(['items', 'submittedBy'])
            ->whereIn('status', [PengajuanDana::STATUS_APPROVED, PengajuanDana::STATUS_REALIZED])
            ->where('divisi', 'perkebunan')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('laporan-realisasi.create', compact(
            'currentMonth', 
            'currentYear', 
            'currentWeek', 
            'peternakanPengajuan', 
            'perkebunanPengajuan'
        ));
    }

    /**
     * Map pengajuan jenis kebutuhan to laporan kategori
     */
    private function mapKategoriFromPengajuan($jenisKebutuhan)
    {
        $mapping = [
            'gaji' => 'tenaga_konsumsi',
            'konsumsi' => 'tenaga_konsumsi',
            'operasional' => 'alat_bahan',
            'alat' => 'alat_bahan',
            'bahan' => 'alat_bahan',
            'pendapatan' => 'pendapatan',
            'penjualan' => 'pendapatan',
        ];
        
        $jenisLower = strtolower($jenisKebutuhan);
        return $mapping[$jenisLower] ?? 'alat_bahan';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'divisi' => 'required|in:peternakan,perkebunan',
            'minggu' => 'nullable|integer|between:1,4',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'keterangan' => 'nullable|string',
            'selected_items' => 'nullable|array',
            'selected_items.*' => 'required|exists:pengajuan_dana_items,id',
            'selected_pendapatan' => 'nullable|array',
            'selected_pendapatan.*' => 'required|exists:pendapatan_susus,id',
            'item_realisasi' => 'nullable|array',
            'item_realisasi.*.jumlah' => 'required|numeric|min:0',
            'item_realisasi.*.harga_satuan' => 'required|numeric|min:0',
            'item_realisasi.*.keterangan' => 'nullable|string',
            'new_items' => 'nullable|array',
            'new_items.*.kategori' => 'required|in:pendapatan,tenaga_konsumsi,alat_bahan',
            'new_items.*.nama_item' => 'required|string|max:255',
            'new_items.*.jumlah' => 'required|numeric|min:0',
            'new_items.*.satuan' => 'required|string|max:50',
            'new_items.*.harga_satuan' => 'required|numeric|min:0',
            'new_items.*.keterangan' => 'nullable|string',
            'new_items.*.is_urgent' => 'nullable|boolean',
            'item_attachments' => 'nullable|array',
            'item_attachments.*' => 'nullable|array',
            'item_attachments.*.*' => 'file|mimes:jpg,jpeg,png,webp,svg,pdf,heic,heif,doc,docx,xls,xlsx,csv,txt|max:20480',
            'pendapatan_attachments' => 'nullable|array',
            'pendapatan_attachments.*' => 'nullable|array',
            'pendapatan_attachments.*.*' => 'file|mimes:jpg,jpeg,png,webp,svg,pdf,heic,heif,doc,docx,xls,xlsx,csv,txt|max:20480',
        ]);

        // Validasi bahwa minimal ada satu item yang dipilih (pengajuan atau pendapatan)
        if ((!$request->has('selected_items') || empty($request->selected_items)) && 
            (!$request->has('selected_pendapatan') || empty($request->selected_pendapatan))) {
            return back()->withErrors(['selected_items' => 'Minimal pilih satu item pengajuan dana atau pendapatan']);
        }

        // Jika minggu tidak dipilih, tentukan minggu berdasarkan tanggal
        $minggu = $request->minggu;
        if (!$minggu) {
            $tanggal = \Carbon\Carbon::parse($request->tanggal);
            $minggu = ceil($tanggal->day / 7);
            $minggu = min($minggu, 4); // Pastikan tidak lebih dari 4
        }

        // Check if laporan already exists for this divisi, minggu, bulan, tahun
        $existingLaporan = LaporanRealisasi::where('divisi', $request->divisi)
            ->where('minggu', $minggu)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        if ($existingLaporan) {
            return back()->withErrors(['divisi' => 'Laporan realisasi untuk divisi, minggu, bulan, dan tahun ini sudah ada']);
        }

        // Create laporan realisasi (tidak perlu status approval)
        $laporanRealisasi = LaporanRealisasi::create([
            'tanggal' => $request->tanggal,
            'divisi' => $request->divisi,
            'minggu' => $minggu,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'submitted_by' => Auth::id(),
            'keterangan' => $request->keterangan,
        ]);

        // Get selected pengajuan items and create laporan realisasi items
        if ($request->has('selected_items') && !empty($request->selected_items)) {
            $selectedItems = \App\Models\PengajuanDanaItem::whereIn('id', $request->selected_items)
                ->with('pengajuanDana')
                ->get();

            foreach ($selectedItems as $pengajuanItem) {
                // Get realisasi data if provided
                $realisasiData = $request->input("item_realisasi.{$pengajuanItem->id}", []);
                
                // Parse formatted numbers
                $jumlahRealisasi = $this->parseFormattedInteger($realisasiData['jumlah'] ?? $pengajuanItem->jumlah);
                $hargaRealisasi = $this->parseFormattedCurrency($realisasiData['harga_satuan'] ?? $pengajuanItem->harga_satuan);
                $keteranganRealisasi = $realisasiData['keterangan'] ?? null;

                $laporanItem = LaporanRealisasiItem::create([
                    'laporan_realisasi_id' => $laporanRealisasi->id,
                    'kategori' => $this->mapKategoriFromPengajuan($pengajuanItem->jenis_kebutuhan),
                    'nama_item' => $pengajuanItem->nama_kebutuhan,
                    'jumlah' => $jumlahRealisasi, // Use realisasi amount
                    'satuan' => $pengajuanItem->satuan,
                    'harga_satuan' => $hargaRealisasi, // Use realisasi price
                    'keterangan' => $keteranganRealisasi ?? $pengajuanItem->keterangan,
                    'minggu' => $minggu, // Set minggu pada item
                ]);

                // Handle attachments for this item
                if ($request->hasFile("item_attachments.{$pengajuanItem->id}")) {
                    foreach ($request->file("item_attachments.{$pengajuanItem->id}") as $file) {
                        if ($file && $file->isValid()) {
                            $storedPath = $file->store('nota', 'public');
                            LaporanRealisasiItemAttachment::create([
                                'laporan_realisasi_item_id' => $laporanItem->id,
                                'path' => $storedPath,
                                'filename' => $file->getClientOriginalName(),
                                'extension' => $file->getClientOriginalExtension(),
                                'mime_type' => $file->getMimeType(),
                                'size' => $file->getSize(),
                                'uploaded_by' => Auth::id(),
                            ]);
                        }
                    }
                }
            }
        }

        // Add new items (urgent purchases)
        if ($request->has('new_items') && !empty($request->new_items)) {
            foreach ($request->new_items as $newItemData) {
                $laporanItem = LaporanRealisasiItem::create([
                    'laporan_realisasi_id' => $laporanRealisasi->id,
                    'kategori' => $newItemData['kategori'],
                    'nama_item' => $newItemData['nama_item'],
                    'jumlah' => $this->parseFormattedInteger($newItemData['jumlah']),
                    'satuan' => $newItemData['satuan'],
                    'harga_satuan' => $this->parseFormattedCurrency($newItemData['harga_satuan']),
                    'keterangan' => ($newItemData['keterangan'] ?? '') . 
                        ($newItemData['is_urgent'] ? ' (Pembelian Urgent - Tidak ada di pengajuan awal)' : ''),
                    'minggu' => $minggu,
                ]);
            }
        }

        // Tambahkan pendapatan yang dichecklist oleh admin
        if ($request->has('selected_pendapatan') && !empty($request->selected_pendapatan)) {
            $selectedPendapatanIds = $request->selected_pendapatan;
            $pendapatanList = \App\Models\PendapatanSusu::whereIn('id', $selectedPendapatanIds)->get();
            
            foreach ($pendapatanList as $pendapatan) {
                $laporanItem = LaporanRealisasiItem::create([
                    'laporan_realisasi_id' => $laporanRealisasi->id,
                    'kategori' => 'pendapatan',
                    'nama_item' => 'Pendapatan ' . ucfirst(str_replace('_', ' ', $pendapatan->jenis_produk)),
                    'jumlah' => $pendapatan->jumlah_liter,
                    'jumlah_realisasi' => $pendapatan->jumlah_liter,
                    'satuan' => $pendapatan->satuan,
                    'harga_satuan' => $pendapatan->harga_per_liter,
                    'keterangan' => 'Otomatis dari data pendapatan susu bulan ini',
                    'nota' => null,
                    'keterangan_realisasi' => null,
                    'minggu' => $minggu,
                ]);

                // Handle attachments for this pendapatan item
                if ($request->hasFile("pendapatan_attachments.{$pendapatan->id}")) {
                    foreach ($request->file("pendapatan_attachments.{$pendapatan->id}") as $file) {
                        if ($file && $file->isValid()) {
                            $storedPath = $file->store('nota', 'public');
                            LaporanRealisasiItemAttachment::create([
                                'laporan_realisasi_item_id' => $laporanItem->id,
                                'path' => $storedPath,
                                'filename' => $file->getClientOriginalName(),
                                'extension' => $file->getClientOriginalExtension(),
                                'mime_type' => $file->getMimeType(),
                                'size' => $file->getSize(),
                                'uploaded_by' => Auth::id(),
                            ]);
                        }
                    }
                }
            }
        }

        // Calculate totals
        $laporanRealisasi->calculateTotals();

        // Trigger event for real-time updates
        event(new LaporanRealisasiUpdated($laporanRealisasi, 'created'));

        return redirect()->route('admin.laporan-realisasi.index')->with('success', 'Laporan realisasi berhasil dibuat dan langsung disetujui');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $laporanRealisasi = LaporanRealisasi::with(['submittedBy', 'items.attachments'])->findOrFail($id);
        
        return view('laporan-realisasi.show', compact('laporanRealisasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $laporanRealisasi = LaporanRealisasi::with(['items.attachments', 'items.pengajuanDanaItem'])->findOrFail($id);
        
        // Get approved and realized pengajuan dana for the same divisi
        $pengajuanDana = PengajuanDana::with(['items', 'submittedBy'])
            ->whereIn('status', [PengajuanDana::STATUS_APPROVED, PengajuanDana::STATUS_REALIZED])
            ->where('divisi', $laporanRealisasi->divisi)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('laporan-realisasi.edit', compact('laporanRealisasi', 'pengajuanDana'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $laporanRealisasi = LaporanRealisasi::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'divisi' => 'required|in:peternakan,perkebunan',
            'minggu' => 'nullable|integer|between:1,4',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.kategori' => 'required|in:pendapatan,tenaga_konsumsi,alat_bahan',
            'items.*.nama_item' => 'required|string|max:255',
            'items.*.jumlah' => 'required|numeric|min:0',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.keterangan' => 'nullable|string',
            'items.*.attachments' => 'sometimes|array',
            'items.*.attachments.*' => 'file|mimes:jpg,jpeg,png,webp,svg,pdf,heic,heif,doc,docx,xls,xlsx,csv,txt|max:20480',
        ]);

        // Jika minggu tidak dipilih, tentukan minggu berdasarkan tanggal
        $minggu = $request->minggu;
        if (!$minggu) {
            $tanggal = \Carbon\Carbon::parse($request->tanggal);
            $minggu = ceil($tanggal->day / 7);
            $minggu = min($minggu, 4); // Pastikan tidak lebih dari 4
        }

        // Update laporan realisasi
        $laporanRealisasi->update([
            'tanggal' => $request->tanggal,
            'divisi' => $request->divisi,
            'minggu' => $minggu,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'keterangan' => $request->keterangan,
        ]);

        // Delete existing items and their attachments files
        foreach ($laporanRealisasi->items as $existingItem) {
            foreach ($existingItem->attachments as $att) {
                try { Storage::disk('public')->delete($att->path); } catch (\Throwable $e) {}
            }
            $existingItem->attachments()->delete();
        }
        $laporanRealisasi->items()->delete();

        // Create new items
        foreach ($request->items as $itemIndex => $item) {
            $newItem = LaporanRealisasiItem::create([
                'laporan_realisasi_id' => $laporanRealisasi->id,
                'kategori' => $item['kategori'],
                'nama_item' => $item['nama_item'],
                'jumlah' => $item['jumlah'],
                'satuan' => $item['satuan'],
                'harga_satuan' => $item['harga_satuan'],
                'keterangan' => $item['keterangan'] ?? null,
                'minggu' => $minggu, // Set minggu pada item
            ]);

            // Handle attachments for this item index
            $filesMatrix = $request->file('items');
            if (isset($filesMatrix[$itemIndex]['attachments']) && is_array($filesMatrix[$itemIndex]['attachments'])) {
                foreach ($filesMatrix[$itemIndex]['attachments'] as $uploaded) {
                    if (!$uploaded) { continue; }
                    $storedPath = $uploaded->store('nota', 'public');
                    LaporanRealisasiItemAttachment::create([
                        'laporan_realisasi_item_id' => $newItem->id,
                        'path' => $storedPath,
                        'filename' => $uploaded->getClientOriginalName(),
                        'extension' => $uploaded->getClientOriginalExtension(),
                        'mime_type' => $uploaded->getClientMimeType(),
                        'size' => $uploaded->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }
        }

        // Calculate totals
        $laporanRealisasi->calculateTotals();

        // Trigger event for real-time updates
        event(new LaporanRealisasiUpdated($laporanRealisasi, 'updated'));

        return redirect()->route('admin.laporan-realisasi.index')->with('success', 'Laporan realisasi berhasil diperbarui');
    }

    /**
     * Update laporan realisasi with advanced editing features (tambah/kurang item, saldo calculation)
     */
    public function updateAdvanced(Request $request, string $id)
    {
        $laporanRealisasi = LaporanRealisasi::with(['items.pengajuanDanaItem'])->findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'divisi' => 'required|in:peternakan,perkebunan',
            'minggu' => 'nullable|integer|between:1,4',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'keterangan' => 'nullable|string',
            'existing_items' => 'nullable|array',
            'existing_items.*.id' => 'required|exists:laporan_realisasi_items,id',
            'existing_items.*.jumlah_realisasi' => 'required|numeric|min:0',
            'existing_items.*.harga_satuan_realisasi' => 'required|numeric|min:0',
            'existing_items.*.keterangan' => 'nullable|string',
            'new_items' => 'nullable|array',
            'new_items.*.kategori' => 'required|in:pendapatan,tenaga_konsumsi,alat_bahan',
            'new_items.*.nama_item' => 'required|string|max:255',
            'new_items.*.jumlah' => 'required|numeric|min:0',
            'new_items.*.satuan' => 'required|string|max:50',
            'new_items.*.harga_satuan' => 'required|numeric|min:0',
            'new_items.*.keterangan' => 'nullable|string',
            'new_items.*.is_urgent' => 'nullable|boolean',
            'item_attachments' => 'nullable|array',
            'item_attachments.*' => 'nullable|array',
            'item_attachments.*.*' => 'file|mimes:jpg,jpeg,png,webp,svg,pdf,heic,heif,doc,docx,xls,xlsx,csv,txt|max:20480',
        ]);

        // Jika minggu tidak dipilih, tentukan minggu berdasarkan tanggal
        $minggu = $request->minggu;
        if (!$minggu) {
            $tanggal = \Carbon\Carbon::parse($request->tanggal);
            $minggu = ceil($tanggal->day / 7);
            $minggu = min($minggu, 4);
        }

        // Update laporan realisasi basic info
        $laporanRealisasi->update([
            'tanggal' => $request->tanggal,
            'divisi' => $request->divisi,
            'minggu' => $minggu,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'keterangan' => $request->keterangan,
        ]);

        $totalPengajuan = 0;
        $totalRealisasi = 0;
        $saldoMinus = 0;
        $sisaSaldo = 0;

        // Update existing items
        if ($request->has('existing_items')) {
            foreach ($request->existing_items as $itemData) {
                $item = LaporanRealisasiItem::findOrFail($itemData['id']);
                
                // Calculate pengajuan amount (original)
                $pengajuanAmount = $item->pengajuanDanaItem ? 
                    ($item->pengajuanDanaItem->jumlah * $item->pengajuanDanaItem->harga_satuan) : 0;
                
                // Parse formatted numbers
                $jumlahRealisasi = $this->parseFormattedInteger($itemData['jumlah_realisasi']);
                $hargaRealisasi = $this->parseFormattedCurrency($itemData['harga_satuan_realisasi']);
                
                // Calculate realisasi amount (updated)
                $realisasiAmount = $jumlahRealisasi * $hargaRealisasi;
                
                // Update item
                $item->update([
                    'jumlah' => $jumlahRealisasi,
                    'harga_satuan' => $hargaRealisasi,
                    'keterangan' => $itemData['keterangan'] ?? $item->keterangan,
                ]);

                $totalPengajuan += $pengajuanAmount;
                $totalRealisasi += $realisasiAmount;

                // Calculate saldo
                if ($realisasiAmount > $pengajuanAmount) {
                    $saldoMinus += ($realisasiAmount - $pengajuanAmount);
                } else {
                    $sisaSaldo += ($pengajuanAmount - $realisasiAmount);
                }
            }
        }

        // Add new items (urgent purchases)
        if ($request->has('new_items')) {
            foreach ($request->new_items as $newItemData) {
                // Parse formatted numbers
                $jumlah = $this->parseFormattedInteger($newItemData['jumlah']);
                $hargaSatuan = $this->parseFormattedCurrency($newItemData['harga_satuan']);
                
                $newItem = LaporanRealisasiItem::create([
                    'laporan_realisasi_id' => $laporanRealisasi->id,
                    'kategori' => $newItemData['kategori'],
                    'nama_item' => $newItemData['nama_item'],
                    'jumlah' => $jumlah,
                    'satuan' => $newItemData['satuan'],
                    'harga_satuan' => $hargaSatuan,
                    'keterangan' => ($newItemData['keterangan'] ?? '') . 
                        ($newItemData['is_urgent'] ? ' (Pembelian Urgent - Tidak ada di pengajuan awal)' : ''),
                    'minggu' => $minggu,
                ]);

                $realisasiAmount = $jumlah * $hargaSatuan;
                $totalRealisasi += $realisasiAmount;
                
                // New items are always considered as saldo minus (not in original pengajuan)
                $saldoMinus += $realisasiAmount;
            }
        }

        // Calculate totals
        $laporanRealisasi->calculateTotals();

        // Trigger event for real-time updates
        event(new LaporanRealisasiUpdated($laporanRealisasi, 'updated'));

        $message = "Laporan realisasi berhasil diperbarui";
        if ($saldoMinus > 0) {
            $message .= ". Saldo minus: Rp " . number_format($saldoMinus, 0, ',', '.') . " (akan diajukan kembali bulan berikutnya)";
        }
        if ($sisaSaldo > 0) {
            $message .= ". Sisa saldo: Rp " . number_format($sisaSaldo, 0, ',', '.');
        }

        return redirect()->route('admin.laporan-realisasi.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaporanRealisasi $laporan_realisasi)
    {
        // Store data before deletion for event
        $bulan = $laporan_realisasi->bulan;
        $tahun = $laporan_realisasi->tahun;
        $divisi = $laporan_realisasi->divisi;

        // Delete items first
        $laporan_realisasi->items()->delete();
        
        // Delete laporan realisasi
        $laporan_realisasi->delete();

        // Trigger event for real-time updates (create temporary object for event)
        $tempLaporan = new LaporanRealisasi([
            'bulan' => $bulan,
            'tahun' => $tahun,
            'divisi' => $divisi
        ]);
        event(new LaporanRealisasiUpdated($tempLaporan, 'deleted'));

        return redirect()->route('admin.laporan-realisasi.index')->with('success', 'Laporan realisasi berhasil dihapus');
    }

    /**
     * Remove a specific item from laporan realisasi
     */
    public function destroyItem(string $laporanRealisasiId, string $itemId)
    {
        $laporanRealisasi = LaporanRealisasi::findOrFail($laporanRealisasiId);

        $item = LaporanRealisasiItem::where('id', $itemId)
            ->where('laporan_realisasi_id', $laporanRealisasiId)
            ->firstOrFail();

        // Delete item attachments first
        $item->attachments()->delete();
        
        // Delete the item
        $item->delete();

        // Recalculate totals
        $laporanRealisasi->calculateTotals();

        // Trigger event for real-time updates
        event(new LaporanRealisasiUpdated($laporanRealisasi, 'item_deleted'));

        return back()->with('success', 'Item berhasil dihapus');
    }


    /**
     * Export to PDF
     */
    public function exportPdf(string $id)
    {
        $laporanRealisasi = LaporanRealisasi::with(['submittedBy', 'items'])->findOrFail($id);
        
        $pdf = PDF::loadView('laporan-realisasi.pdf', compact('laporanRealisasi'));
        
        return $pdf->download("laporan-realisasi-{$laporanRealisasi->divisi}-week{$laporanRealisasi->minggu}-{$laporanRealisasi->bulan}-{$laporanRealisasi->tahun}.pdf");
    }

    /**
     * Export to Excel
     */
    public function exportExcel(string $id)
    {
        $laporanRealisasi = LaporanRealisasi::with(['submittedBy', 'items'])->findOrFail($id);
        
        return Excel::download(new LaporanRealisasiExport($laporanRealisasi), "laporan-realisasi-{$laporanRealisasi->divisi}-week{$laporanRealisasi->minggu}-{$laporanRealisasi->bulan}-{$laporanRealisasi->tahun}.xlsx");
    }

    /**
     * Export Rekapan to PDF
     */
    public function exportRekapanPdf(string $id)
    {
        $laporanRealisasi = LaporanRealisasi::findOrFail($id);
        
        // Check if laporan is approved
        // No approval check needed - all laporan can be exported
        
        // Get or create rekapan laporan
        try {
            $rekapanLaporan = RekapanLaporan::generateFromApprovedLaporan(
                $laporanRealisasi->bulan,
                $laporanRealisasi->tahun,
                $laporanRealisasi->divisi,
                auth()->id()
            );
            // Load all reports for this period to gather attachments
            $approvedWeekly = LaporanRealisasi::with(['items.attachments'])
                ->where('bulan', $laporanRealisasi->bulan)
                ->where('tahun', $laporanRealisasi->tahun)
                ->where('divisi', $laporanRealisasi->divisi)
                ->get();
            
            $pdf = PDF::loadView('rekapan-laporan.pdf', [
                'rekapanLaporan' => $rekapanLaporan,
                'approvedWeekly' => $approvedWeekly,
            ]);
            
            return $pdf->download("rekapan-laporan-{$laporanRealisasi->divisi}-{$laporanRealisasi->bulan}-{$laporanRealisasi->tahun}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat rekapan laporan: ' . $e->getMessage());
        }
    }

    /**
     * Export Rekapan to Excel
     */
    public function exportRekapanExcel(string $id)
    {
        $laporanRealisasi = LaporanRealisasi::findOrFail($id);
        
        // Check if laporan is approved
        // No approval check needed - all laporan can be exported
        
        // Get or create rekapan laporan
        try {
            $rekapanLaporan = RekapanLaporan::generateFromApprovedLaporan(
                $laporanRealisasi->bulan,
                $laporanRealisasi->tahun,
                $laporanRealisasi->divisi,
                auth()->id()
            );
            
            return Excel::download(new RekapanLaporanExport($rekapanLaporan), "rekapan-laporan-{$laporanRealisasi->divisi}-{$laporanRealisasi->bulan}-{$laporanRealisasi->tahun}.xlsx");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat rekapan laporan: ' . $e->getMessage());
        }
    }

    /**
     * Rekap Index (UI untuk export rekap bulanan)
     */
    public function rekapIndex(Request $request)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $years = range($currentYear - 5, $currentYear + 2);
        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        
        // Get filter parameters
        $filterDivisi = $request->get('divisi', 'all');
        $filterBulan = $request->get('bulan', $currentMonth);
        $filterTahun = $request->get('tahun', $currentYear);
        
        // Get existing rekapan data for current month/year
        $rekapanData = RekapanLaporan::where('periode_bulan', $currentMonth)
            ->where('periode_tahun', $currentYear)
            ->with(['items', 'generatedBy'])
            ->get();
            
        // If no rekapan data exists, try to generate empty ones for display
        if ($rekapanData->isEmpty()) {
            try {
                $userId = auth()->id() ?: 1;
                RekapanLaporan::generateCombinedForMonth($currentMonth, $currentYear, (int) $userId);
                $rekapanData = RekapanLaporan::where('periode_bulan', $currentMonth)
                    ->where('periode_tahun', $currentYear)
                    ->with(['items', 'generatedBy'])
                    ->get();
            } catch (\Exception $e) {
                // If still no data, continue with empty collection
            }
        } else {
            // Refresh existing rekapan data to ensure real-time calculation
            foreach ($rekapanData as $rekapan) {
                RekapanLaporan::refreshRekapan($rekapan->periode_bulan, $rekapan->periode_tahun, $rekapan->divisi);
            }
        }
            
        // Get all rekapan data for display with filters
        $query = RekapanLaporan::with(['items', 'generatedBy']);
        
        // Apply divisi filter
        if ($filterDivisi !== 'all') {
            $query->where('divisi', $filterDivisi);
        }
        
        // Apply bulan filter
        if ($filterBulan) {
            $query->where('periode_bulan', $filterBulan);
        }
        
        // Apply tahun filter
        if ($filterTahun) {
            $query->where('periode_tahun', $filterTahun);
        }
        
        $allRekapan = $query->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->orderBy('divisi')
            ->paginate(15);
        
        // Clean up empty rekapan data
        $this->cleanupEmptyRekapan();

        return view('rekapan-laporan.index', compact(
            'years', 
            'currentYear', 
            'currentMonth', 
            'months', 
            'rekapanData', 
            'allRekapan',
            'filterDivisi',
            'filterBulan',
            'filterTahun'
        ));
    }

    /**
     * Clean up empty rekapan data
     */
    private function cleanupEmptyRekapan(): void
    {
        $rekapans = RekapanLaporan::all();
        
        foreach ($rekapans as $rekapan) {
            $debit = RekapanLaporan::computeDebit($rekapan->periode_bulan, $rekapan->periode_tahun, $rekapan->divisi);
            $kredit = RekapanLaporan::computeKredit($rekapan->periode_bulan, $rekapan->periode_tahun, $rekapan->divisi);
            $pendapatan = RekapanLaporan::computePendapatan($rekapan->periode_bulan, $rekapan->periode_tahun);
            
            // If all values are 0, delete the rekapan
            if ($debit == 0 && $kredit == 0 && $pendapatan == 0) {
                $rekapan->delete();
            }
        }
    }

    /**
     * Show detail rekapan
     */
    public function showRekapan($id)
    {
        $rekapan = RekapanLaporan::with(['items', 'generatedBy'])
            ->findOrFail($id);
            
        return view('rekapan-laporan.show', compact('rekapan'));
    }



    /**
     * Export Rekapan Bulanan to Excel
     */
    public function exportRekapanBulananExcel(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'divisi' => 'required|in:all,peternakan,perkebunan'
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $divisi = $request->divisi;

        if ($divisi === 'all') {
            $rekapan = \App\Models\RekapanLaporan::generateCombinedForMonth($bulan, $tahun, auth()->id());
            // Bungkus ke koleksi by-month agar kompatibel dengan export
            $rekapanByMonth = [$bulan => [$rekapan]];
            return Excel::download(new RekapanLaporanBulananExport($rekapanByMonth), "rekapan-gabungan-{$tahun}-{$bulan}.xlsx");
        }

        // Per divisi: gunakan generator existing
        $rekapan = \App\Models\RekapanLaporan::generateFromApprovedLaporan($bulan, $tahun, $divisi, auth()->id());
        $rekapanByMonth = [$bulan => [$rekapan]];
        return Excel::download(new RekapanLaporanBulananExport($rekapanByMonth), "rekapan-{$divisi}-{$tahun}-{$bulan}.xlsx");
    }

    /**
     * Create combined rekapan by month (combining all divisions)
     */
    private function createCombinedRekapanByMonth($approvedLaporans, $tahun)
    {
        $rekapanLaporansByMonth = [];
        
        foreach ($approvedLaporans->groupBy('bulan') as $month => $laporans) {
            $rekapanLaporansByMonth[$month] = [];
            
            // Combine all divisions for this month
            $allItems = collect();
            foreach ($laporans as $laporan) {
                foreach ($laporan->items as $item) {
                    $allItems->push([
                        'nama_item' => $item->nama_item,
                        'kategori' => $item->kategori,
                        'jumlah' => $item->jumlah,
                        'harga_satuan' => $item->harga_satuan,
                        'satuan' => $item->satuan,
                        'minggu' => $item->minggu,
                    ]);
                }
            }
            
            // Create a combined rekapan for this month
            try {
                $rekapanLaporan = new \App\Models\RekapanLaporan();
                $rekapanLaporan->bulan = $month;
                $rekapanLaporan->tahun = $tahun;
                $rekapanLaporan->divisi = 'combined';
                $rekapanLaporan->minggu = 1; // Default minggu
                $rekapanLaporan->status = 'finalized';
                $rekapanLaporan->generated_by = auth()->id();
                $rekapanLaporan->generated_at = now();
                
                // Calculate totals
                $totalPendapatan = $allItems->whereIn('kategori', ['pendapatan', 'penjualan'])->sum(function($item) {
                    return $item['jumlah'] * $item['harga_satuan'];
                });
                
                $totalBiaya = $allItems->whereNotIn('kategori', ['pendapatan', 'penjualan'])->sum(function($item) {
                    return $item['jumlah'] * $item['harga_satuan'];
                });
                
                $rekapanLaporan->total_pendapatan = $totalPendapatan;
                $rekapanLaporan->total_biaya = $totalBiaya;
                $rekapanLaporan->total_saldo = $totalPendapatan - $totalBiaya;
                
                // Create rekapan items from all items
                $rekapanItems = [];
                foreach ($allItems as $item) {
                    $rekapanItems[] = [
                        'rekapan_laporan_id' => null, // Will be set after save
                        'nama_item' => $item['nama_item'],
                        'kategori' => $item['kategori'],
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $item['harga_satuan'],
                        'satuan' => $item['satuan'],
                        'minggu' => $item['minggu'],
                    ];
                }
                
                $rekapanLaporan->items = collect($rekapanItems);
                $rekapanLaporansByMonth[$month][] = $rekapanLaporan;
                
            } catch (\Exception $e) {
                throw new \Exception('Gagal membuat rekapan laporan untuk bulan ' . $month . ': ' . $e->getMessage());
            }
        }
        
        return $rekapanLaporansByMonth;
    }

    /**
     * Export Rekapan Bulanan to PDF (single month)
     */
    public function exportRekapanBulananPdf(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'divisi' => 'required|in:all,peternakan,perkebunan'
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $divisi = $request->divisi;

        if ($divisi === 'all') {
            $rekapan = \App\Models\RekapanLaporan::generateCombinedForMonth($bulan, $tahun, auth()->id());
        } else {
            $rekapan = \App\Models\RekapanLaporan::generateFromApprovedLaporan($bulan, $tahun, $divisi, auth()->id());
        }

        $approvedWeekly = LaporanRealisasi::with(['items.attachments'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->when($divisi !== 'combined', fn($q) => $q->where('divisi', $divisi))
            ->get();

        $pdf = PDF::loadView('rekapan-laporan.pdf', [
            'rekapanLaporan' => $rekapan,
            'approvedWeekly' => $approvedWeekly,
        ]);
        $name = $divisi === 'combined' ? 'rekapan-gabungan' : 'rekapan-' . $divisi;
        return $pdf->download("{$name}-{$tahun}-{$bulan}.pdf");
    }

    /**
     * Delete all Laporan Realisasi (admin bulk action)
     */
    public function deleteAll()
    {
        try {
            DB::transaction(function () {
                // Eager load items and attachments to minimize queries
                $all = LaporanRealisasi::with(['items.attachments'])->get();
                foreach ($all as $laporan) {
                    foreach ($laporan->items as $item) {
                        foreach ($item->attachments as $att) {
                            try { Storage::disk('public')->delete($att->path); } catch (\Throwable $e) {}
                        }
                        $item->attachments()->delete();
                    }
                    $laporan->items()->delete();
                }
                // Finally delete laporans
                LaporanRealisasi::query()->delete();
            });
            return redirect()->route('admin.laporan-realisasi.index')->with('success', 'Berhasil menghapus semua laporan realisasi');
        } catch (\Exception $e) {
            return redirect()->route('admin.laporan-realisasi.index')->with('error', 'Gagal menghapus semua laporan: ' . $e->getMessage());
        }
    }

    /**
     * Parse formatted integer (no decimals)
     */
    private function parseFormattedInteger($value)
    {
        // Handle formatted integer - just convert to int
        // JavaScript already handles formatting, so we just need to convert
        if (is_numeric($value)) {
            return (int) $value;
        }
        
        return 0;
    }

    /**
     * Parse formatted number (with comma as decimal separator)
     */
    private function parseFormattedNumber($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        // Handle formatted number like "1.234,56"
        $value = str_replace('.', '', $value); // Remove thousand separators
        $value = str_replace(',', '.', $value); // Replace comma with dot for decimal
        
        return (float) $value;
    }

    /**
     * Parse formatted currency (with dots as thousand separators)
     */
    private function parseFormattedCurrency($value)
    {
        // Handle formatted currency like "1.234.567" or "1.400"
        // Always remove dots first, then check if numeric
        $value = str_replace('.', '', $value); // Remove thousand separators
        
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        return 0;
    }
}
