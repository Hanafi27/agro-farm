<!DOCTYPE html>
<html>
<head>
    <title>Test View</title>
</head>
<body>
    <h1>Test Button Export Rekapan</h1>
    
    @php
        $laporan = \App\Models\LaporanRealisasi::where('status', 'approved')->first();
        $user = \App\Models\User::where('role', 'admin')->first();
    @endphp
    
    <p>Laporan ID: {{ $laporan->id }}</p>
    <p>Status: {{ $laporan->status }}</p>
    <p>isApproved(): {{ $laporan->isApproved() ? 'true' : 'false' }}</p>
    <p>User Role: {{ $user->role }}</p>
    
    <h2>Test Button Admin:</h2>
    @if($user->role === 'admin')
        <p>✅ User adalah admin</p>
        @if($laporan->isApproved())
            <p>✅ Laporan sudah approved</p>
            <p>Button export rekapan seharusnya muncul:</p>
            <a href="{{ route('admin.laporan-realisasi.export-rekapan-pdf', $laporan->id) }}" style="background: purple; color: white; padding: 10px; margin: 5px; display: inline-block;">
                Export Rekapan PDF
            </a>
            <a href="{{ route('admin.laporan-realisasi.export-rekapan-excel', $laporan->id) }}" style="background: indigo; color: white; padding: 10px; margin: 5px; display: inline-block;">
                Export Rekapan Excel
            </a>
        @else
            <p>❌ Laporan belum approved</p>
        @endif
    @else
        <p>❌ User bukan admin</p>
    @endif
    
    <h2>Test Button Owner:</h2>
    @if($user->role === 'owner')
        <p>✅ User adalah owner</p>
        @if($laporan->isApproved())
            <p>✅ Laporan sudah approved</p>
            <p>Button export rekapan seharusnya muncul:</p>
            <a href="{{ route('owner.laporan-realisasi.export-rekapan-pdf', $laporan->id) }}" style="background: purple; color: white; padding: 10px; margin: 5px; display: inline-block;">
                Export Rekapan PDF
            </a>
            <a href="{{ route('owner.laporan-realisasi.export-rekapan-excel', $laporan->id) }}" style="background: indigo; color: white; padding: 10px; margin: 5px; display: inline-block;">
                Export Rekapan Excel
            </a>
        @else
            <p>❌ Laporan belum approved</p>
        @endif
    @else
        <p>❌ User bukan owner</p>
    @endif
    
    <h2>Test Button Keuangan:</h2>
    @if($user->role === 'keuangan')
        <p>✅ User adalah keuangan</p>
        @if($laporan->isApproved())
            <p>✅ Laporan sudah approved</p>
            <p>Button export rekapan seharusnya muncul:</p>
            <a href="{{ route('keuangan.laporan-realisasi.export-rekapan-pdf', $laporan->id) }}" style="background: purple; color: white; padding: 10px; margin: 5px; display: inline-block;">
                Export Rekapan PDF
            </a>
            <a href="{{ route('keuangan.laporan-realisasi.export-rekapan-excel', $laporan->id) }}" style="background: indigo; color: white; padding: 10px; margin: 5px; display: inline-block;">
                Export Rekapan Excel
            </a>
        @else
            <p>❌ Laporan belum approved</p>
        @endif
    @else
        <p>❌ User bukan keuangan</p>
    @endif
</body>
</html>
