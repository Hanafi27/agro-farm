<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciwidey Agro Farm - Sistem Informasi Peternakan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-green-400 via-blue-500 to-purple-600 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/10 backdrop-blur-md border-b border-white/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 overflow-hidden">
                        <img src="{{ asset('asset/logo.png') }}" alt="Ciwidey Agro Farm Logo" class="w-full h-full object-cover">
                    </div>
                    <h1 class="text-white text-xl font-bold">Ciwidey Agro Farm</h1>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('login') }}" class="text-white hover:text-green-200 transition duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-white/20 text-white px-4 py-2 rounded-lg hover:bg-white/30 transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <div class="bg-white/20 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8 overflow-hidden">
                    <img src="{{ asset('asset/logo.png') }}" alt="Ciwidey Agro Farm Logo" class="w-full h-full object-cover">
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                    Ciwidey Agro Farm
                </h1>
                <p class="text-xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Sistem Informasi Peternakan - Platform terpadu untuk mengelola data karyawan, absensi, penggajian, pendapatan, 
                    pengajuan dana, dan laporan realisasi dengan sistem role-based access control.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}" 
                        class="bg-white text-green-600 font-bold py-4 px-8 rounded-lg hover:bg-gray-100 transition duration-200 transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>Mulai Sekarang
                    </a>
                    <a href="{{ route('register') }}" 
                        class="bg-transparent border-2 border-white text-white font-bold py-4 px-8 rounded-lg hover:bg-white/10 transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i>Daftar Akun
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white/10 backdrop-blur-md py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-white mb-4">Fitur Utama</h2>
                <p class="text-white/80">Sistem yang dirancang untuk memudahkan pengelolaan peternakan Ciwidey Agro Farm</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white/20 backdrop-blur-md rounded-lg p-6 text-center">
                    <div class="bg-white/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Manajemen Pegawai</h3>
                    <p class="text-white/80">Kelola data karyawan dengan informasi lengkap termasuk divisi dan gaji pokok</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white/20 backdrop-blur-md rounded-lg p-6 text-center">
                    <div class="bg-white/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-check text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Sistem Absensi</h3>
                    <p class="text-white/80">Pencatatan kehadiran harian dengan perhitungan jam kerja otomatis</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white/20 backdrop-blur-md rounded-lg p-6 text-center">
                    <div class="bg-white/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Penggajian Otomatis</h3>
                    <p class="text-white/80">Perhitungan gaji berdasarkan absensi dan bonus kerja</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white/20 backdrop-blur-md rounded-lg p-6 text-center">
                    <div class="bg-white/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-milk text-white text-2xl"></i>
                    </div>
                                            <h3 class="text-xl font-semibold text-white mb-2">Pendapatan</h3>
                    <p class="text-white/80">Tracking hasil panen susu harian per pegawai atau kelompok</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white/20 backdrop-blur-md rounded-lg p-6 text-center">
                    <div class="bg-white/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hand-holding-usd text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Pengajuan Dana</h3>
                    <p class="text-white/80">Sistem pengajuan dana dengan workflow approval yang lengkap</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white/20 backdrop-blur-md rounded-lg p-6 text-center">
                    <div class="bg-white/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Laporan Realisasi</h3>
                    <p class="text-white/80">Laporan penggunaan dana berdasarkan pengajuan yang disetujui</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black/20 backdrop-blur-md py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-white/60">&copy; 2024 Ciwidey Agro Farm. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
