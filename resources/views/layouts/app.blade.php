<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ciwidey Agro Farm')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'agro-green': '#10B981',
                        'agro-blue': '#3B82F6',
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out" id="sidebar">
        <!-- Logo Section -->
        <div class="flex items-center justify-center h-16 bg-gradient-to-r from-agro-green to-agro-blue">
            <div class="w-8 h-8 rounded-full overflow-hidden mr-3">
                <img src="{{ asset('asset/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
            </div>
            <span class="text-white font-bold text-lg">Ciwidey Agro Farm</span>
        </div>

        <!-- Navigation Menu -->
        <nav class="mt-8 px-4">
            <div class="space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3 {{ request()->routeIs('dashboard') ? 'text-agro-green' : '' }}"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                @if(auth()->user()->role === 'admin')
                <!-- Manajemen Pegawai -->
                <a href="{{ route('admin.pegawai.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.pegawai.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-users mr-3 {{ request()->routeIs('admin.pegawai.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Manajemen Pegawai</span>
                </a>

                <!-- Absensi Pegawai -->
                <a href="{{ route('admin.absensi.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.absensi.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-clock mr-3 {{ request()->routeIs('admin.absensi.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Absensi Pegawai</span>
                </a>

                <!-- Pendapatan -->
                <a href="{{ route('admin.pendapatan-susu.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.pendapatan-susu.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-milk-bottle mr-3 {{ request()->routeIs('admin.pendapatan-susu.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Pendapatan</span>
                </a>

                <!-- Penggajian -->
                <a href="{{ route('admin.penggajian.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.penggajian.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-money-bill-wave mr-3 {{ request()->routeIs('admin.penggajian.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Penggajian</span>
                </a>

                <!-- Pengajuan Dana -->
                <a href="{{ route('admin.pengajuan-dana.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.pengajuan-dana.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-hand-holding-usd mr-3 {{ request()->routeIs('admin.pengajuan-dana.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Pengajuan Dana</span>
                </a>

                <!-- Laporan (dropdown) -->
                <div>
                    <button type="button" onclick="toggleLaporanSubmenu()" class="w-full flex items-center justify-between px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors">
                        <span class="flex items-center">
                            <i class="fas fa-folder-open mr-3"></i>
                            <span class="font-medium">Laporan</span>
                        </span>
                        <i id="laporan-caret" class="fas fa-chevron-down text-xs transition-transform"></i>
                    </button>
                    @php
                        $laporanActive = request()->routeIs('admin.laporan-realisasi.*') || request()->routeIs('admin.laporan-rekap.*') || request()->is('admin/laba-rugi*');
                    @endphp
                    <div id="laporan-submenu" class="ml-8 space-y-1 {{ $laporanActive ? '' : 'hidden' }}">
                        <a href="{{ route('admin.laporan-realisasi.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.laporan-realisasi.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                            <i class="fas fa-file-alt mr-3 {{ request()->routeIs('admin.laporan-realisasi.*') ? 'text-agro-green' : '' }}"></i>
                            <span>Laporan Realisasi</span>
                        </a>
                        <a href="{{ route('admin.laporan-rekap.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('admin.laporan-rekap.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                            <i class="fas fa-file-invoice mr-3 {{ request()->routeIs('admin.laporan-rekap.*') ? 'text-agro-green' : '' }}"></i>
                            <span>Laporan Rekap</span>
                        </a>
                        <a href="{{ url('admin/laba-rugi') }}" class="flex items-center px-4 py-2 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->is('admin/laba-rugi*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                            <i class="fas fa-scale-balanced mr-3 {{ request()->is('admin/laba-rugi*') ? 'text-agro-green' : '' }}"></i>
                            <span>Laporan Laba Rugi</span>
                        </a>
                    </div>
                </div>

                
                @endif

                @if(auth()->user()->role === 'owner')
                <!-- Owner Menu -->
                <a href="{{ route('owner.pengajuan-dana.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('owner.pengajuan-dana.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-hand-holding-usd mr-3 {{ request()->routeIs('owner.pengajuan-dana.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Pengajuan Dana</span>
                </a>

                <a href="{{ route('owner.laporan-realisasi.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('owner.laporan-realisasi.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-chart-line mr-3 {{ request()->routeIs('owner.laporan-realisasi.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Laporan Realisasi</span>
                </a>

                <a href="{{ route('owner.laporan-rekap.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('owner.laporan-rekap.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-file-invoice mr-3 {{ request()->routeIs('owner.laporan-rekap.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Laporan Rekap</span>
                </a>

                <a href="{{ url('owner/laba-rugi') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->is('owner/laba-rugi*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-scale-balanced mr-3 {{ request()->is('owner/laba-rugi*') ? 'text-agro-green' : '' }}"></i>
                    <span>Laporan Laba Rugi</span>
                </a>
                
                @endif

                @if(auth()->user()->role === 'keuangan')
                <!-- Keuangan Menu -->
                <a href="{{ route('keuangan.pengajuan-dana.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('keuangan.pengajuan-dana.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-hand-holding-usd mr-3 {{ request()->routeIs('keuangan.pengajuan-dana.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Pengajuan Dana</span>
                </a>



                <a href="{{ route('keuangan.laporan-realisasi.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('keuangan.laporan-realisasi.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-chart-line mr-3 {{ request()->routeIs('keuangan.laporan-realisasi.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Laporan Realisasi</span>
                </a>

                <a href="{{ route('keuangan.laporan-rekap.index') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->routeIs('keuangan.laporan-rekap.*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-file-invoice mr-3 {{ request()->routeIs('keuangan.laporan-rekap.*') ? 'text-agro-green' : '' }}"></i>
                    <span>Laporan Rekap</span>
                </a>

                <a href="{{ url('keuangan/laba-rugi') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-gray-700 rounded-lg transition-colors {{ request()->is('keuangan/laba-rugi*') ? 'bg-green-50 border-l-4 border-agro-green text-gray-700' : '' }}">
                    <i class="fas fa-scale-balanced mr-3 {{ request()->is('keuangan/laba-rugi*') ? 'text-agro-green' : '' }}"></i>
                    <span>Laporan Laba Rugi</span>
                </a>
                
                @endif
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64 min-h-screen flex flex-col">
        <!-- Top Navbar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <!-- Mobile menu button -->
                <button class="lg:hidden" onclick="toggleSidebar()">
                    <i class="fas fa-bars text-gray-600"></i>
                </button>

                <!-- Page Title -->
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</div>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-r from-agro-green to-agro-blue rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600 transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-6">
            <div class="flex items-center justify-between px-6">
                <div class="flex items-center">
                    <div class="w-6 h-6 rounded-full overflow-hidden mr-3">
                        <img src="{{ asset('asset/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
                    </div>
                    <span class="text-gray-600 font-medium">Ciwidey Agro Farm</span>
                </div>
                <div class="text-gray-500 text-sm">
                    © 2025 Ciwidey Agro Farm. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        function toggleLaporanSubmenu() {
            const submenu = document.getElementById('laporan-submenu');
            const caret = document.getElementById('laporan-caret');
            const isHidden = submenu.classList.contains('hidden');
            if (isHidden) {
                submenu.classList.remove('hidden');
                caret.classList.add('rotate-180');
            } else {
                submenu.classList.add('hidden');
                caret.classList.remove('rotate-180');
            }
        }

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = event.target.closest('button');
            
            if (!sidebar.contains(event.target) && !toggleButton && window.innerWidth < 1024) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
