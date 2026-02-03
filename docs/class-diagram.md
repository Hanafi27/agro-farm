classDiagram

%% MODELS
class User {
  +id: int
  +name: string
  +email: string
  +password: string
  +role: string
  %% CRUD (via Eloquent)
  +create()
  +find()
  +update()
  +delete()
}

class Pegawai {
  +id: int
  +user_id: int
  +nama: string
  +divisi: string
  +kontak: string
  +alamat: text
  +gaji_pokok: decimal(10,2)
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Relations
  +user()
  +absensis()
  +penggajians()
  +pendapatanSusus()
  +pengajuanDanas()
}

class Absensi {
  +id: int
  +pegawai_id: int
  +tanggal: date
  +jam_masuk: time|null
  +jam_keluar: time|null
  +status: enum(hadir,izin,alpha)
  +keterangan: text|null
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Helpers
  +isHadir()
  +isIzin()
  +isAlfa()
  +getStatusLabel()
  +getStatusBadgeClass()
}

class PendapatanSusu {
  +id: int
  +tanggal: date
  +kategori: enum(perkebunan,peternakan)
  +jenis_produk: enum(teh,susu_kambing,susu_sapi)
  +jumlah_liter: int
  +satuan: string
  +harga_per_liter: int
  +total_pendapatan: int
  +keterangan: text|null
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Helpers
  +getKategoriLabel()
  +getJenisProdukLabel()
  +getKategoriBadgeClass()
  +getJenisProdukBadgeClass()
}

class PengajuanDana {
  +id: int
  +tanggal: date
  +divisi: enum(peternakan,perkebunan)
  +bulan: int
  +tahun: int
  +status: enum(draft,submit,approved,realized,rejected)
  +submitted_by: int|null
  +approved_by: int|null
  +rejected_by: int|null
  +alasan_rejection: text|null
  +tanggal_approval: datetime|null
  +realized_by: int|null
  +tanggal_realisasi: datetime|null
  +nominal_diberikan: decimal(10,2)|null
  +keterangan: text|null
  +bukti_transfer: string|null
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Relations
  +submittedBy()
  +approvedBy()
  +rejectedBy()
  +realizedBy()
  +items()
  %% Helpers
  +getStatusLabel()
  +getStatusBadgeClass()
  +getDivisiLabel()
  +getBulanLabel()
  +getTotalAmount()
  +isDraft()/isPending()/isApproved()/isRejected()/isRealized()
}

class PengajuanDanaItem {
  +id: int
  +pengajuan_dana_id: int
  +jenis_kebutuhan: enum(operasional,gaji,konsumsi,lainnya)
  +nama_kebutuhan: string
  +jumlah: decimal(10,2)
  +satuan: string
  +harga_satuan: decimal(10,2)
  +keterangan: text|null
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Relations
  +pengajuanDana()
  %% Helpers
  +getJenisKebutuhanLabel()
  +getTotalAmount()
  +getFormattedTotalAmount()
  +getFormattedHargaSatuan()
}

class LaporanRealisasi {
  +id: int
  +tanggal: date
  +divisi: enum(peternakan,perkebunan)
  +minggu: int
  +bulan: int
  +tahun: int
  +submitted_by: int|null
  +keterangan: text|null
  +total_pendapatan: decimal(10,2)
  +total_tenaga_konsumsi: decimal(10,2)
  +total_alat_bahan: decimal(10,2)
  +total_biaya: decimal(10,2)
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Relations
  +submittedBy()
  +items()
  %% Helpers
  +getDivisiLabel()
  +getMingguLabel()
  +getBulanLabel()
  +calculateTotals()
}

class LaporanRealisasiItem {
  +id: int
  +laporan_realisasi_id: int
  +kategori: enum(pendapatan,tenaga_konsumsi,alat_bahan)
  +nama_item: string
  +jumlah: decimal(10,2)
  +jumlah_realisasi: decimal(10,2)|null
  +satuan: string
  +harga_satuan: decimal(10,2)
  +keterangan: text|null
  +nota: string|null
  +keterangan_realisasi: text|null
  +minggu: int|null
  +pengajuan_dana_item_id: int|null
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Relations
  +laporanRealisasi()
  +pengajuanDanaItem()
  +attachments()
  %% Helpers
  +getKategoriLabel()
  +getTotalAmount()
  +getFormattedTotalAmount()
  +getFormattedHargaSatuan()
  +getFormattedJumlah()
}

class LaporanRealisasiItemAttachment {
  +id: int
  +laporan_realisasi_item_id: int
  +path: string
  +filename: string
  +extension: string
  +mime_type: string
  +size: int
  +uploaded_by: int|null
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Relations
  +item()
}

class RekapanLaporan {
  +id: int
  +periode_bulan: int
  +periode_tahun: int
  +divisi: enum(peternakan,perkebunan,combined)
  +total_pendapatan: decimal(10,2)
  +total_tenaga_konsumsi: decimal(10,2)
  +total_alat_bahan: decimal(10,2)
  +total_biaya: decimal(10,2)
  +generated_by: int|null
  +generated_at: datetime|null
  +keterangan: text|null
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Relations
  +generatedBy()
  +items()
  %% Helpers
  +getDivisiLabel()
  +getPeriodeLabel()
  +getBulanLabel()
  +calculateTotals()
  +getDebitAmount()/getKreditAmount()/getSaldoAmount()
  +refreshRekapan(bulan,tahun,divisi)
  +generateFromApprovedLaporan()
  +generateCombinedForMonth()
}

class RekapanLaporanItem {
  +id: int
  +rekapan_laporan_id: int
  +kategori: enum(pendapatan,tenaga_konsumsi,alat_bahan)
  +nama_item: string
  +jumlah: decimal(10,2)
  +satuan: string
  +harga_satuan: decimal(10,2)
  +keterangan: text|null
  +minggu: int|null
  +laporan_realisasi_id: int|null
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Relations
  +rekapanLaporan()
  %% Helpers
  +getKategoriLabel()
  +getTotalAmount()
  +getFormattedTotalAmount()
}

class Penggajian {
  +id: int
  +pegawai_id: int
  +bulan: int
  +tahun: int
  +tipe_periode: string
  +tanggal: date
  +gaji_per_bulan: int
  +gaji_per_minggu: int
  +total_hadir: int
  +total_izin: int
  +total_alfa: int
  +potongan: int
  +total_gaji: int
  +keterangan: text|null
  %% CRUD
  +create()
  +find()
  +update()
  +delete()
  %% Relations
  +pegawai()
}

%% CONTROLLERS (CRUD methods)
class PegawaiController {
  +index()
  +create()
  +store(Request)
  +show(id)
  +edit(id)
  +update(Request,id)
  +destroy(id)
}

class AbsensiController {
  +index(Request)
  +create()
  +store(Request)
  +show(id)
  +edit(id)
  +update(Request,id)
  +destroy(id)
  +deleteAll()
  +exportPdf(Request)
  +exportExcel(Request)
}

class PendapatanSusuController {
  +index()
  +create()
  +store(Request)
  +show(id)
  +edit(id)
  +update(Request,id)
  +destroy(id)
  +bulkDelete()
  +getByMonth()
}

class PengajuanDanaController {
  +index()
  +create()
  +store(Request)
  +show(id)
  +edit(id)
  +update(Request,id)
  +destroy(id)
  +history()
  +send(id)
  +sendAllDraft()
  +approve(id)
  +reject(id)
  +realize(id)
  +bulkDelete()
  +bulkDeleteHistory()
  +deleteHistory(id)
  +getByMonth()
}

class LaporanRealisasiController {
  +index()
  +create()
  +store(Request)
  +show(id)
  +edit(id)
  +update(Request,id)
  +updateAdvanced(id)
  +destroy(id)
  +deleteAll()
  +send(id)
  +approve(id)
  +rekapIndex()
  +showRekapan(id)
  +exportPdf(id)
  +exportExcel(id)
  +exportRekapanPdf(id)
  +exportRekapanExcel(id)
  +exportRekapanBulananPdf(Request)
  +exportRekapanBulananExcel(Request)
}

class LabaRugiController {
  +index()
  +exportPdf()
  +exportExcel()
}

%% RELATIONSHIPS
User "1" -- "0..1" Pegawai : hasOne
Pegawai "1" -- "*" Absensi : hasMany
Pegawai "1" -- "*" Penggajian : hasMany
Pegawai "1" -- "*" PendapatanSusu : hasMany
Pegawai "1" -- "*" PengajuanDana : hasMany

PengajuanDana "1" -- "*" PengajuanDanaItem : hasMany
LaporanRealisasi "1" -- "*" LaporanRealisasiItem : hasMany
LaporanRealisasiItem "1" -- "*" LaporanRealisasiItemAttachment : hasMany

RekapanLaporan "1" -- "*" RekapanLaporanItem : hasMany

PengajuanDanaItem "0..1" -- "*" LaporanRealisasiItem : referencedBy
```

Catatan:
- Metode CRUD pada model bersifat konseptual (create/find/update/delete via Eloquent ORM).
- Relasi mengikuti method di model Eloquent yang ada pada kode Anda.
- Controller menampilkan metode CRUD plus aksi tambahan (bulk, export, approve, dsb) sesuai implementasi routes dan controller.
