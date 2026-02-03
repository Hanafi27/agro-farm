<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Penggajian;

echo "Latest 10 penggajian:\n";
$latest = Penggajian::orderBy('id','desc')->limit(10)->get();
foreach ($latest as $p) {
    echo sprintf("#%d pegawai:%d tipe:%s tanggal:%s bulan:%s tahun:%s total:%s ket:%s\n",
        $p->id,
        $p->pegawai_id,
        $p->tipe_periode,
        optional($p->tanggal)->toDateString(),
        $p->bulan,
        $p->tahun,
        $p->total_gaji,
        $p->keterangan
    );
}

echo "\nPenggajian tipe rentang (20 terbaru):\n";
$ranges = Penggajian::where('tipe_periode','rentang')->orderBy('id','desc')->limit(20)->get();
foreach ($ranges as $p) {
    echo sprintf("#%d pegawai:%d rentang ket:%s total_hadir:%d potongan:%d total:%d\n",
        $p->id,
        $p->pegawai_id,
        $p->keterangan,
        (int)$p->total_hadir,
        (int)$p->potongan,
        (int)$p->total_gaji
    );
}
