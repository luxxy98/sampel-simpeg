---
description: Panduan lengkap untuk mengecek alur aplikasi dari Absensi sampai Gaji
---

# Panduan Testing Alur Absensi → Gaji

Panduan ini menjelaskan langkah-langkah untuk mengetes fitur aplikasi SIMPEG mulai dari input absensi hingga penggajian.

---

## ⚙️ Prasyarat

1. **Server Laravel berjalan**
   ```powershell
   cd c:\laragon\www\sampel-simpeg
   php artisan serve
   ```
   Akses aplikasi di: `http://127.0.0.1:8000`

2. **Database terkoneksi** dengan benar (simpeg & absensigaji)

---

## 🔧 Script Otomatis (Quick Check)

Jalankan script pengecekan cepat yang sudah tersedia:

// turbo
```powershell
cd c:\laragon\www\sampel-simpeg
php test_workflow.php
```

Script ini akan mengecek:
- ✅ Koneksi database
- ✅ Data SDM aktif
- ✅ Data absensi
- ✅ Periode gaji
- ✅ Komponen gaji
- ✅ Transaksi gaji
- ✅ Distribusi gaji
- ✅ Rekening SDM

---

## 📋 Testing Manual via Browser

### TAHAP 1: Cek Data SDM

1. **Login** ke aplikasi dengan kredensial admin
2. Navigasi ke menu **SDM** → lihat daftar pegawai
3. Pastikan ada SDM dengan:
   - Jabatan aktif (tanggal keluar kosong)
   - Rekening terdaftar (untuk distribusi gaji)

**URL:** `http://127.0.0.1:8000/admin/sdm`

---

### TAHAP 2: Cek & Input Absensi

1. Navigasi ke menu **Absensi**
2. **Cek data yang ada:**
   - Filter berdasarkan tanggal & pegawai
   - Pastikan data ditampilkan di tabel

3. **Test Create Absensi:**
   - Klik tombol **Tambah Absensi**
   - Pilih SDM, jadwal, jenis absen
   - Isi tanggal dan jam
   - Jika ada lembur, isi durasi lembur
   - Submit dan verifikasi berhasil tersimpan

4. **Test Edit Absensi:**
   - Klik ikon edit pada salah satu data
   - Ubah beberapa field
   - Submit dan verifikasi perubahan

5. **Test Detail Absensi:**
   - Klik ikon view untuk melihat detail
   - Verifikasi semua informasi ditampilkan dengan benar

**URL:** `http://127.0.0.1:8000/admin/absensi`

---

### TAHAP 3: Setup Komponen Gaji (Jika Belum Ada)

1. Navigasi ke menu **Gaji** → **Komponen**
2. Pastikan setiap jabatan memiliki komponen gaji:
   - Gaji Pokok
   - Tunjangan (jika ada)
   - Potongan (jika ada)

**URL:** `http://127.0.0.1:8000/admin/gaji/komponen`

---

### TAHAP 4: Cek Tarif Lembur (Jika Diperlukan)

1. Navigasi ke **Gaji** → **Tarif Lembur**
2. Pastikan tarif lembur sudah tersetting untuk jabatan yang memiliki lembur

**URL:** `http://127.0.0.1:8000/admin/gaji/tarif-lembur`

---

### TAHAP 5: Buat Periode Gaji

1. Navigasi ke **Gaji** → **Periode**
2. **Buat periode baru** (jika belum ada):
   - Klik **Tambah Periode**
   - Pilih bulan dan tahun
   - Submit

3. **Generate Gaji:**
   - Pada periode yang diinginkan, klik tombol **Generate**
   - Sistem akan menghitung gaji berdasarkan:
     - Komponen gaji per jabatan
     - Data absensi (potongan untuk absen tertentu)
     - Lembur (jika ada)
   - Tunggu proses selesai

**URL:** `http://127.0.0.1:8000/admin/gaji/periode`

---

### TAHAP 6: Cek Transaksi Gaji

1. Navigasi ke **Gaji** → **Transaksi**
2. Filter berdasarkan periode yang baru di-generate
3. Verifikasi:
   - Daftar pegawai dengan gaji yang dihitung
   - Total penghasilan sesuai komponen
   - Total potongan (termasuk dari absensi)
   - Lembur terintegrasi (jika ada)
   - Take Home Pay benar

4. **Lihat Detail:**
   - Klik ikon detail pada salah satu transaksi
   - Verifikasi breakdown komponen gaji
   - Pastikan nominal lembur muncul (jika ada)

**URL:** `http://127.0.0.1:8000/admin/gaji/trx`

---

### TAHAP 7: Distribusi Gaji (Opsional)

1. Navigasi ke **Gaji** → **Distribusi**
2. Buat distribusi untuk periode yang sudah di-generate
3. Verifikasi:
   - Data rekening pegawai terisi
   - Jumlah transfer sesuai THP
   - Status distribusi dapat di-update

**URL:** `http://127.0.0.1:8000/admin/gaji/distribusi`

---

## ✅ Checklist Testing

| #  | Item                                      | Status |
|----|-------------------------------------------|--------|
| 1  | Login berhasil                            | ☐      |
| 2  | Data SDM aktif tersedia                   | ☐      |
| 3  | Absensi dapat ditampilkan                 | ☐      |
| 4  | Absensi dapat ditambah                    | ☐      |
| 5  | Absensi dapat diedit                      | ☐      |
| 6  | Detail absensi dapat dilihat              | ☐      |
| 7  | Komponen gaji tersedia per jabatan        | ☐      |
| 8  | Tarif lembur tersetting (jika perlu)      | ☐      |
| 9  | Periode gaji dapat dibuat                 | ☐      |
| 10 | Generate gaji berhasil                    | ☐      |
| 11 | Transaksi gaji muncul setelah generate    | ☐      |
| 12 | Detail transaksi menampilkan breakdown    | ☐      |
| 13 | Lembur terintegrasi di detail gaji        | ☐      |
| 14 | Distribusi gaji berfungsi                 | ☐      |

---

## 🐛 Troubleshooting

### Error 500 pada halaman?
- Cek log: `php artisan serve` atau lihat `storage/logs/laravel.log`
- Pastikan semua migrasi sudah dijalankan

### Data tidak muncul di tabel?
- Buka DevTools browser (F12) → tab Network
- Cek response dari endpoint `data` (misal: `/admin/absensi/data`)
- Pastikan tidak ada error JSON

### Generate gaji gagal?
- Pastikan SDM memiliki jabatan aktif (`tanggal_keluar` = NULL)
- Pastikan komponen gaji untuk jabatan tersebut sudah ada
- Cek koneksi database `absensigaji`

### Lembur tidak muncul di gaji?
- Pastikan data lembur sudah diinput di absensi
- Cek tarif lembur untuk jabatan bersangkutan
- Pastikan periode gaji di-generate ulang setelah input lembur

---

## 📁 File Terkait

- **Controllers:**
  - `app/Http/Controllers/Admin/Absensi/AbsensiController.php`
  - `app/Http/Controllers/Admin/Gaji/GajiPeriodeController.php`
  - `app/Http/Controllers/Admin/Gaji/GajiTrxController.php`
  - `app/Http/Controllers/Admin/Gaji/GajiDistribusiController.php`

- **Services:**
  - `app/Services/Absensi/AbsensiService.php`
  - `app/Services/Gaji/GajiTrxService.php`
  - `app/Services/Gaji/GajiDistribusiService.php`

- **Test Script:**
  - `test_workflow.php` - Script CLI untuk quick check

---

## 🔗 Alur Data

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│     SDM      │────▶│   Absensi    │────▶│  Periode     │
│  (Pegawai)   │     │  (Kehadiran) │     │    Gaji      │
└──────────────┘     └──────────────┘     └──────────────┘
       │                    │                    │
       │                    │                    ▼
       │                    │             ┌──────────────┐
       │                    └────────────▶│   Generate   │
       │                                  │     Gaji     │
       │                                  └──────────────┘
       │                                         │
       ▼                                         ▼
┌──────────────┐                          ┌──────────────┐
│  Komponen    │─────────────────────────▶│  Transaksi   │
│    Gaji      │                          │     Gaji     │
└──────────────┘                          └──────────────┘
                                                 │
                                                 ▼
                                          ┌──────────────┐
                                          │  Distribusi  │
                                          │     Gaji     │
                                          └──────────────┘
```
