<?php

namespace App\Http\Controllers;

use App\Models\PengajuanDana;
use App\Models\PengajuanDanaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengajuanDanaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Filter pengajuan dana based on user role
        $query = PengajuanDana::with(['submittedBy', 'approvedBy', 'rejectedBy', 'realizedBy', 'items']);
        
        if ($user->role === 'keuangan') {
            // Keuangan melihat semua pengajuan yang sudah disetujui (approved dan realized)
            $query->whereIn('status', [PengajuanDana::STATUS_APPROVED, PengajuanDana::STATUS_REALIZED]);
        } else {
            // Admin dan Owner melihat semua pengajuan
            // Tidak ada filter status untuk admin dan owner
        }
        
        $pengajuanDanas = $query->orderBy('created_at', 'desc')->paginate(10);

        // Return different views based on user role
        if ($user->role === 'owner') {
            return view('pengajuan-dana.index-owner', compact('pengajuanDanas'));
        } elseif ($user->role === 'keuangan') {
            return view('pengajuan-dana.index-keuangan', compact('pengajuanDanas'));
        } else {
            return view('pengajuan-dana.index', compact('pengajuanDanas'));
        }
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
        
        return view('pengajuan-dana.create', compact('currentMonth', 'currentYear'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'divisi' => 'required|in:peternakan,perkebunan',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.jenis_kebutuhan' => 'required|in:operasional,gaji,konsumsi,lainnya',
            'items.*.nama_kebutuhan' => 'required|string|max:255',
            'items.*.jumlah' => 'required|numeric|min:0',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.keterangan' => 'nullable|string',
        ]);

        // Check if pengajuan already exists for this divisi, bulan, tahun
        $existingPengajuan = PengajuanDana::where('divisi', $request->divisi)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        if ($existingPengajuan) {
            return back()->withErrors(['divisi' => 'Pengajuan untuk divisi, bulan, dan tahun ini sudah ada']);
        }

        // Create pengajuan dana
        $pengajuanDana = PengajuanDana::create([
            'tanggal' => $request->tanggal,
            'divisi' => $request->divisi,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'status' => PengajuanDana::STATUS_DRAFT,
            'submitted_by' => Auth::id(),
            'keterangan' => $request->keterangan,
        ]);

        // Create items
        foreach ($request->items as $item) {
            PengajuanDanaItem::create([
                'pengajuan_dana_id' => $pengajuanDana->id,
                'jenis_kebutuhan' => $item['jenis_kebutuhan'],
                'nama_kebutuhan' => $item['nama_kebutuhan'],
                'jumlah' => $item['jumlah'],
                'satuan' => $item['satuan'],
                'harga_satuan' => $item['harga_satuan'],
                'keterangan' => $item['keterangan'] ?? null,
            ]);
        }

        return redirect()->route('admin.pengajuan-dana.index')->with('success', 'Pengajuan dana berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuanDana = PengajuanDana::with(['submittedBy', 'approvedBy', 'rejectedBy', 'realizedBy', 'items'])->findOrFail($id);
        return view('pengajuan-dana.show', compact('pengajuanDana'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pengajuanDana = PengajuanDana::with('items')->findOrFail($id);
        
        // Only allow editing if status is draft
        if ($pengajuanDana->status !== PengajuanDana::STATUS_DRAFT) {
            return redirect()->route('admin.pengajuan-dana.index')->with('error', 'Pengajuan tidak dapat diedit karena sudah diproses');
        }

        return view('pengajuan-dana.edit', compact('pengajuanDana'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pengajuanDana = PengajuanDana::findOrFail($id);
        
        // Only allow updating if status is draft
        if ($pengajuanDana->status !== PengajuanDana::STATUS_DRAFT) {
            return redirect()->route('admin.pengajuan-dana.index')->with('error', 'Pengajuan tidak dapat diupdate karena sudah diproses');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'divisi' => 'required|in:peternakan,perkebunan',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.jenis_kebutuhan' => 'required|in:operasional,gaji,konsumsi,lainnya',
            'items.*.nama_kebutuhan' => 'required|string|max:255',
            'items.*.jumlah' => 'required|numeric|min:0',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.keterangan' => 'nullable|string',
        ]);

        // Check if pengajuan already exists for this divisi, bulan, tahun (excluding current)
        $existingPengajuan = PengajuanDana::where('divisi', $request->divisi)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $id)
            ->first();

        if ($existingPengajuan) {
            return back()->withErrors(['divisi' => 'Pengajuan untuk divisi, bulan, dan tahun ini sudah ada']);
        }

        // Update pengajuan dana
        $pengajuanDana->update([
            'tanggal' => $request->tanggal,
            'divisi' => $request->divisi,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'keterangan' => $request->keterangan,
        ]);

        // Delete existing items
        $pengajuanDana->items()->delete();

        // Create new items
        foreach ($request->items as $item) {
            PengajuanDanaItem::create([
                'pengajuan_dana_id' => $pengajuanDana->id,
                'jenis_kebutuhan' => $item['jenis_kebutuhan'],
                'nama_kebutuhan' => $item['nama_kebutuhan'],
                'jumlah' => $item['jumlah'],
                'satuan' => $item['satuan'],
                'harga_satuan' => $item['harga_satuan'],
                'keterangan' => $item['keterangan'] ?? null,
            ]);
        }

        return redirect()->route('admin.pengajuan-dana.index')->with('success', 'Pengajuan dana berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $pengajuanDana = PengajuanDana::findOrFail($id);
        
        // Check if user has permission to delete this pengajuan
        if ($user->role === 'admin') {
            // Admin can delete any pengajuan
        } elseif ($user->role === 'owner') {
            // Owner can delete pengajuan that are not realized
            if ($pengajuanDana->status === PengajuanDana::STATUS_REALIZED) {
                return response()->json(['success' => false, 'message' => 'Pengajuan yang sudah direalisasikan tidak dapat dihapus'], 403);
            }
        } elseif ($user->role === 'keuangan') {
            // Keuangan can only delete realized pengajuan
            if ($pengajuanDana->status !== PengajuanDana::STATUS_REALIZED) {
                return response()->json(['success' => false, 'message' => 'Hanya pengajuan yang sudah direalisasikan yang dapat dihapus'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang untuk menghapus pengajuan'], 403);
        }
        
        try {
            // Delete related items first
            $pengajuanDana->items()->delete();
            
            // Delete the pengajuan
            $pengajuanDana->delete();
            
            return response()->json(['success' => true, 'message' => 'Pengajuan dana berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus pengajuan dana: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Send pengajuan for approval (Admin only)
     */
    public function send(string $id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang untuk mengirim pengajuan'], 403);
        }
        
        $pengajuanDana = PengajuanDana::findOrFail($id);
        
        if ($pengajuanDana->status !== PengajuanDana::STATUS_DRAFT) {
            return response()->json(['success' => false, 'message' => 'Pengajuan tidak dapat dikirim karena status tidak valid'], 400);
        }
        
        $pengajuanDana->update([
            'status' => PengajuanDana::STATUS_SUBMIT,
        ]);

        return response()->json(['success' => true, 'message' => 'Pengajuan dana berhasil dikirim untuk persetujuan']);
    }

    /**
     * Approve pengajuan (Owner only)
     */
    public function approve(string $id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang untuk menyetujui pengajuan'], 403);
        }
        
        $pengajuanDana = PengajuanDana::findOrFail($id);
        
        if ($pengajuanDana->status !== PengajuanDana::STATUS_SUBMIT) {
            return response()->json(['success' => false, 'message' => 'Pengajuan tidak dapat disetujui karena status tidak valid'], 400);
        }
        
        $pengajuanDana->update([
            'status' => PengajuanDana::STATUS_APPROVED,
            'approved_by' => $user->id,
            'tanggal_approval' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Pengajuan dana berhasil disetujui']);
    }

    /**
     * Reject pengajuan (Owner only)
     */
    public function reject(Request $request, string $id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'owner') {
            return redirect()->route('owner.pengajuan-dana.index')->with('error', 'Anda tidak memiliki wewenang untuk menolak pengajuan');
        }
        
        $request->validate([
            'alasan_rejection' => 'required|string|max:500',
        ]);
        
        $pengajuanDana = PengajuanDana::findOrFail($id);
        
        if ($pengajuanDana->status !== PengajuanDana::STATUS_SUBMIT) {
            return redirect()->route('owner.pengajuan-dana.index')->with('error', 'Pengajuan tidak dapat ditolak karena status tidak valid');
        }
        
        $pengajuanDana->update([
            'status' => PengajuanDana::STATUS_REJECTED,
            'rejected_by' => $user->id,
            'alasan_rejection' => $request->alasan_rejection,
            'tanggal_approval' => now(),
        ]);

        return redirect()->route('owner.pengajuan-dana.index')->with('success', 'Pengajuan dana berhasil ditolak');
    }

    /**
     * Realize pengajuan (Keuangan only)
     */
    public function realize(Request $request, string $id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'keuangan') {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk merealisasikan pengajuan');
        }
        
        $request->validate([
            'tanggal_realisasi' => 'required|date',
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $pengajuanDana = PengajuanDana::findOrFail($id);
        
        if ($pengajuanDana->status !== PengajuanDana::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat direalisasikan karena belum disetujui');
        }
        
        // Handle file upload with robust error handling
        $buktiTransferPath = null;
        try {
            if ($request->hasFile('bukti_transfer')) {
                $file = $request->file('bukti_transfer');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Use direct move method instead of storeAs
                $storageDir = storage_path('app/public/bukti_transfer');
                $filePath = $storageDir . '/' . $fileName;
                
                \Log::info("Moving file to: {$filePath}");
                
                // Move file directly to storage
                $moved = $file->move($storageDir, $fileName);
                
                if (!$moved) {
                    throw new \Exception('Gagal memindahkan file ke storage');
                }
                
                \Log::info("File moved successfully to: " . $moved->getPathname());
                
                // Verify file exists
                if (!file_exists($filePath)) {
                    throw new \Exception('File tidak ditemukan setelah pemindahan. Path: ' . $filePath);
                }
                
                $buktiTransferPath = 'bukti_transfer/' . $fileName;
                
                // Log successful upload
                \Log::info("File uploaded successfully: {$fileName} for pengajuan ID: {$id}");
            } else {
                throw new \Exception('File bukti transfer tidak ditemukan dalam request');
            }
        } catch (\Exception $e) {
            \Log::error("File upload failed for pengajuan ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Upload file gagal: ' . $e->getMessage());
        }
        
        // Use the total amount from items automatically
        $totalAmount = $pengajuanDana->getTotalAmount();
        
        try {
            $pengajuanDana->update([
                'status' => PengajuanDana::STATUS_REALIZED,
                'realized_by' => $user->id,
                'tanggal_realisasi' => $request->tanggal_realisasi,
                'nominal_diberikan' => $totalAmount,
                'bukti_transfer' => $buktiTransferPath,
            ]);
            
            // Log successful realization
            \Log::info("Pengajuan ID {$id} realized successfully with bukti transfer: {$buktiTransferPath}");
            
            return redirect()->back()->with('success', 'Pengajuan dana berhasil direalisasikan dengan bukti transfer');
        } catch (\Exception $e) {
            \Log::error("Failed to update pengajuan ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Show pengajuan dana history
     */
    public function history()
    {
        $user = auth()->user();
        
        // Filter pengajuan dana history based on user role
        $query = PengajuanDana::with(['submittedBy', 'approvedBy', 'rejectedBy', 'realizedBy', 'items']);
        
        if ($user->role === 'owner') {
            // Owner melihat histori: rejected, realized (yang sudah selesai diproses)
            $query->whereIn('status', [PengajuanDana::STATUS_REJECTED, PengajuanDana::STATUS_REALIZED]);
        } elseif ($user->role === 'keuangan') {
            // Keuangan hanya melihat histori: realized (dana dicairkan)
            $query->where('status', PengajuanDana::STATUS_REALIZED);
        } else {
            // Admin melihat semua histori: rejected, realized (yang sudah selesai diproses)
            $query->whereIn('status', [PengajuanDana::STATUS_REJECTED, PengajuanDana::STATUS_REALIZED]);
        }
        
        $pengajuanDanas = $query->orderBy('created_at', 'desc')->paginate(10);

        // Return different views based on user role
        if ($user->role === 'owner') {
            return view('pengajuan-dana.history-owner', compact('pengajuanDanas'));
        } elseif ($user->role === 'keuangan') {
            return view('pengajuan-dana.history-keuangan', compact('pengajuanDanas'));
        } else {
            return view('pengajuan-dana.history', compact('pengajuanDanas'));
        }
    }

    /**
     * Delete single history item
     */
    public function deleteHistory(string $id)
    {
        $user = auth()->user();
        $pengajuanDana = PengajuanDana::findOrFail($id);
        
        // Check if user has permission to delete this item
        if ($user->role === 'admin' || 
            ($user->role === 'owner' && in_array($pengajuanDana->status, [PengajuanDana::STATUS_APPROVED, PengajuanDana::STATUS_REJECTED, PengajuanDana::STATUS_REALIZED])) ||
            ($user->role === 'keuangan' && $pengajuanDana->status === PengajuanDana::STATUS_REALIZED)) {
            
            $pengajuanDana->delete();
            
            return response()->json(['success' => true, 'message' => 'Riwayat pengajuan berhasil dihapus']);
        }
        
        return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang untuk menghapus riwayat ini'], 403);
    }

    /**
     * Bulk delete history items
     */
    public function bulkDeleteHistory(Request $request)
    {
        $user = auth()->user();
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada item yang dipilih']);
        }
        
        $query = PengajuanDana::whereIn('id', $ids);
        
        // Apply role-based filtering
        if ($user->role === 'owner') {
            $query->whereIn('status', [PengajuanDana::STATUS_APPROVED, PengajuanDana::STATUS_REJECTED, PengajuanDana::STATUS_REALIZED]);
        } elseif ($user->role === 'keuangan') {
            $query->where('status', PengajuanDana::STATUS_REALIZED);
        }
        // Admin can delete all history items
        
        $deletedCount = $query->delete();
        
        return response()->json(['success' => true, 'message' => "Berhasil menghapus {$deletedCount} riwayat pengajuan"]);
    }

    /**
     * Send all draft pengajuan dana milik admin yang sedang login
     */
    public function sendAllDraft(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk mengirim semua draft.');
        }
        $count = PengajuanDana::where('status', PengajuanDana::STATUS_DRAFT)
            ->where('submitted_by', $user->id)
            ->update(['status' => PengajuanDana::STATUS_SUBMIT]);
        return redirect()->back()->with('success', "Berhasil mengirim {$count} pengajuan dana draft menjadi submit.");
    }

    /**
     * Owner: Setujui semua pengajuan dana yang statusnya submit
     */
    public function approveAllSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'owner') {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk menyetujui semua pengajuan.');
        }
        $count = PengajuanDana::where('status', PengajuanDana::STATUS_SUBMIT)
            ->update([
                'status' => PengajuanDana::STATUS_APPROVED,
                'approved_by' => $user->id,
                'tanggal_approval' => now(),
            ]);
        return redirect()->back()->with('success', "Berhasil menyetujui {$count} pengajuan dana.");
    }

    /**
     * Keuangan: Cairkan semua pengajuan dana yang statusnya approved
     * Method ini dihapus karena keuangan harus menyertakan bukti transfer untuk setiap pencairan
     */
    // public function realizeAllApproved()
    // {
    //     // Method dihapus karena tidak sesuai dengan requirement
    //     // Keuangan harus upload bukti transfer untuk setiap pencairan dana
    // }



    /**
     * Bulk delete pengajuan dana
     */
    public function bulkDelete(Request $request)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'owner', 'keuangan'])) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang untuk menghapus pengajuan'], 403);
        }
        
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:pengajuan_danas,id'
        ]);
        
        $pengajuanDanas = PengajuanDana::whereIn('id', $request->ids)->get();
        $deletedCount = 0;
        
        foreach ($pengajuanDanas as $pengajuanDana) {
            // Check permissions for each pengajuan
            if ($user->role === 'admin') {
                // Admin can delete any pengajuan
            } elseif ($user->role === 'owner') {
                // Owner can delete pengajuan that are not realized
                if ($pengajuanDana->status === PengajuanDana::STATUS_REALIZED) {
                    continue; // Skip this one
                }
            } elseif ($user->role === 'keuangan') {
                // Keuangan can only delete realized pengajuan
                if ($pengajuanDana->status !== PengajuanDana::STATUS_REALIZED) {
                    continue; // Skip this one
                }
            }
            
            try {
                // Delete related items first
                $pengajuanDana->items()->delete();
                
                // Delete the pengajuan
                $pengajuanDana->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                // Continue with other pengajuan even if one fails
                continue;
            }
        }
        
        return response()->json([
            'success' => true, 
            'message' => "Berhasil menghapus {$deletedCount} pengajuan dana"
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

        $pengajuan = PengajuanDana::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->where('divisi', $request->divisi)
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
            'count' => count($allItems)
        ]);
    }
}
