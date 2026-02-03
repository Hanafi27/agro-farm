<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\PengajuanDana;
use App\Models\Penggajian;
use App\Models\PendapatanSusu;
use App\Models\LaporanRealisasi;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get statistics based on user role
        $stats = $this->getStats($user);
        
        // Get navigation menu based on role
        $navigation = $this->getNavigation($user);
        
        return view('dashboard', compact('stats', 'navigation', 'user'));
    }
    
    private function getStats($user)
    {
        $stats = [];
        
        // Common stats for all roles (hitung semua pegawai)
        $stats['total_pegawai'] = Pegawai::count();
        $stats['total_pengajuan_dana'] = PengajuanDana::count();
        
        // Admin can see all stats
        if ($user->role === 'admin') {
            $stats['total_absensi'] = Absensi::count();
            $stats['total_pendapatan_susu'] = PendapatanSusu::count();
            $stats['total_penggajian'] = Penggajian::count();
            $stats['total_laporan_realisasi'] = LaporanRealisasi::count();
            $stats['pending_pengajuan'] = PengajuanDana::where('status', 'pending')->count();
            $stats['draft_pengajuan'] = PengajuanDana::where('status', 'draft')->count();
        }
        
        // Owner and Keuangan can see financial stats
        if (in_array($user->role, ['owner', 'keuangan'])) {
            $stats['total_laporan_realisasi'] = LaporanRealisasi::count();
            $stats['pending_pengajuan'] = PengajuanDana::where('status', 'pending')->count();
            $stats['approved_pengajuan'] = PengajuanDana::where('status', 'approved')->count();
        }
        
        return $stats;
    }
    
    private function getNavigation($user)
    {
        $navigation = [
            'dashboard' => [
                'name' => 'Dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'url' => route('dashboard'),
                'active' => true
            ]
        ];
        
        // Admin features (only admin)
        if ($user->role === 'admin') {
            $navigation['pegawai'] = [
                'name' => 'Manajemen Pegawai',
                'icon' => 'fas fa-users',
                'url' => route('admin.pegawai.index'),
                'active' => false
            ];
            
            $navigation['absensi'] = [
                'name' => 'Absensi Pegawai',
                'icon' => 'fas fa-clock',
                'url' => route('admin.absensi.index'),
                'active' => false
            ];
            
            $navigation['pendapatan_susu'] = [
                'name' => 'Pendapatan Susu',
                'icon' => 'fas fa-milk-bottle',
                'url' => route('admin.pendapatan-susu.index'),
                'active' => false
            ];
            
            $navigation['penggajian'] = [
                'name' => 'Penggajian',
                'icon' => 'fas fa-money-bill-wave',
                'url' => route('admin.penggajian.index'),
                'active' => false
            ];
            
            $navigation['pengajuan_dana'] = [
                'name' => 'Pengajuan Dana',
                'icon' => 'fas fa-hand-holding-usd',
                'url' => route('admin.pengajuan-dana.index'),
                'active' => false
            ];
            
            $navigation['laporan_realisasi'] = [
                'name' => 'Laporan Realisasi',
                'icon' => 'fas fa-chart-line',
                'url' => route('admin.laporan-realisasi.index'),
                'active' => false
            ];
        }
        
        // Owner features
        if ($user->role === 'owner') {
            $navigation['pengajuan_dana'] = [
                'name' => 'Pengajuan Dana',
                'icon' => 'fas fa-hand-holding-usd',
                'url' => route('owner.pengajuan-dana.index'),
                'active' => false
            ];
            
            $navigation['laporan_realisasi'] = [
                'name' => 'Laporan Realisasi',
                'icon' => 'fas fa-chart-line',
                'url' => route('owner.laporan-realisasi.index'),
                'active' => false
            ];
            

        }
        
        // Keuangan features
        if ($user->role === 'keuangan') {
            $navigation['pengajuan_dana'] = [
                'name' => 'Pengajuan Dana',
                'icon' => 'fas fa-hand-holding-usd',
                'url' => route('keuangan.pengajuan-dana.index'),
                'active' => false
            ];
            

            
            $navigation['laporan_realisasi'] = [
                'name' => 'Laporan Realisasi',
                'icon' => 'fas fa-chart-line',
                'url' => route('keuangan.laporan-realisasi.index'),
                'active' => false
            ];
        }
        
        return $navigation;
    }
}
