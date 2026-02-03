<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pegawai::with('user')
            ->orderBy('created_at', 'desc'); // Urutkan berdasarkan created_at descending

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%");
        }

        // Filter by divisi
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        $pegawais = $query->paginate(50); // Increase per page to show more data
        
        // Debug information - remove this after fixing
        if (config('app.debug')) {
            \Log::info('Pegawai Query Debug', [
                'total_pegawai' => $pegawais->total(),
                'current_page' => $pegawais->currentPage(),
                'per_page' => $pegawais->perPage(),
                'items_count' => $pegawais->count(),
                'first_item' => $pegawais->first() ? $pegawais->first()->nama : 'No items'
            ]);
        }
        
        return view('pegawai.index', compact('pegawais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pegawai.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'divisi' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'alamat' => 'required|string',
            'gaji_pokok' => 'required|numeric|min:0',
        ]);

        // Create user with nama as name
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password
            'role' => 'pegawai', // Default role
        ]);

        // Create pegawai
        $user->pegawai()->create([
            'nama' => $request->nama,
            'divisi' => $request->divisi,
            'kontak' => $request->kontak,
            'alamat' => $request->alamat,
            'gaji_pokok' => $request->gaji_pokok,
        ]);

        return redirect()->route('admin.pegawai.index')->with('success', 'Pegawai berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pegawai = Pegawai::with('user')->findOrFail($id);
        return view('pegawai.show', compact('pegawai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pegawai = Pegawai::with('user')->findOrFail($id);
        return view('pegawai.edit', compact('pegawai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $pegawai->user_id,
            'divisi' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'alamat' => 'required|string',
        ]);

        // Update user with nama as name
        $pegawai->user->update([
            'name' => $request->nama,
            'email' => $request->email,
        ]);

        // Update pegawai (gaji_pokok tidak dapat diubah melalui aplikasi)
        $pegawai->update([
            'nama' => $request->nama,
            'divisi' => $request->divisi,
            'kontak' => $request->kontak,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('admin.pegawai.index')->with('success', 'Pegawai berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->user->delete(); // This will also delete pegawai due to cascade

        return redirect()->route('admin.pegawai.index')->with('success', 'Pegawai berhasil dihapus');
    }
}
