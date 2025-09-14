# 📋 MANUAL PENGGUNA APLIKASI APP PKP

## Sistem Manajemen Kinerja Pegawai Pengadilan Agama

---

## 📖 DAFTAR ISI

### [1. PENDAHULUAN](#1-pendahuluan)

-   [1.1 Tujuan Aplikasi](#11-tujuan-aplikasi)
-   [1.2 Fitur Utama](#12-fitur-utama)
-   [1.3 Peran Pengguna](#13-peran-pengguna)

### [2. PERSIAPAN PENGGUNAAN](#2-persiapan-penggunaan)

-   [2.1 Persyaratan Sistem](#21-persyaratan-sistem)
-   [2.2 Instalasi dan Setup](#22-instalasi-dan-setup)
-   [2.3 Akses Aplikasi](#23-akses-aplikasi)

### [3. PANDUAN PENGGUNA SUPER ADMIN](#3-pandu-an-pengguna-super-admin)

-   [3.1 Login dan Dashboard](#31-login-dan-dashboard)
-   [3.2 Manajemen Unit Kerja](#32-manajemen-unit-kerja)
-   [3.3 Manajemen Pegawai](#33-manajemen-pegawai)
-   [3.4 Manajemen Perjanjian Kinerja](#34-manajemen-perjanjian-kinerja)
-   [3.5 Manajemen Penilaian Bulanan](#35-manajemen-penilaian-bulanan)
-   [3.6 Laporan dan Ekspor](#36-laporan-dan-ekspor)

### [4. PANDUAN PENGGUNA PEJABAT PENILAI](#4-pandu-an-pengguna-pejabat-penilai)

-   [4.1 Login dan Dashboard](#41-login-dan-dashboard)
-   [4.2 Membuat Perjanjian Kinerja](#42-membuat-perjanjian-kinerja)
-   [4.3 Mengelola Penilaian Bulanan](#43-mengelola-penilaian-bulanan)
-   [4.4 Upload Dokumen](#44-upload-dokumen)

### [5. PANDUAN PENGGUNA ATASAN PEJABAT PENILAI](#5-pandu-an-pengguna-atasan-pejabat-penilai)

-   [5.1 Login dan Dashboard](#51-login-dan-dashboard)
-   [5.2 Menyetujui Perjanjian Kinerja](#52-menyetujui-perjanjian-kinerja)
-   [5.3 Menyetujui Penilaian Bulanan](#53-menyetujui-penilaian-bulanan)
-   [5.4 Monitoring Kinerja](#54-monitoring-kinerja)

### [6. PANDUAN PENGGUNA PEGAWAI](#6-pandu-an-pengguna-pegawai)

-   [6.1 Login dan Dashboard](#61-login-dan-dashboard)
-   [6.2 Membuat Perjanjian Kinerja Sendiri](#62-membuat-perjanjian-kinerja-sendiri)
-   [6.3 Melakukan Penilaian Bulanan](#63-melakukan-penilaian-bulanan)
-   [6.4 Mengupload Dokumen Pendukung](#64-mengupload-dokumen-pendukung)

### [7. PANDUAN IMPORT DATA](#7-pandu-an-import-data)

-   [7.1 Template Import Pegawai](#71-template-import-pegawai)
-   [7.2 Proses Import](#72-proses-import)
-   [7.3 Validasi Data](#73-validasi-data)

### [8. PANDUAN PELAPORAN](#8-pandu-an-pelaporan)

-   [8.1 Laporan Perjanjian Kinerja](#81-laporan-perjanjian-kinerja)
-   [8.2 Laporan Penilaian Bulanan](#82-laporan-penilaian-bulanan)
-   [8.3 Laporan Rekapitulasi](#83-laporan-rekapitulasi)

### [9. TROUBLESHOOTING](#9-troubleshooting)

-   [9.1 Masalah Login](#91-masalah-login)
-   [9.2 Error Upload File](#92-error-upload-file)
-   [9.3 Error Import Data](#93-error-import-data)
-   [9.4 Error Generate PDF](#94-error-generate-pdf)

### [10. FAQ](#10-faq)

---

## 1. PENDAHULUAN

### 1.1 Tujuan Aplikasi

**APP PKP (Perjanjian Kinerja Pegawai)** adalah aplikasi manajemen kinerja pegawai yang dirancang khusus untuk instansi pemerintah, khususnya Pengadilan Agama. Aplikasi ini bertujuan untuk:

-   ✅ Mengelola perjanjian kinerja individu pegawai
-   ✅ Memantau pencapaian target kinerja bulanan
-   ✅ Menghasilkan laporan kinerja sesuai standar pemerintah
-   ✅ Memfasilitasi proses approval dan monitoring kinerja
-   ✅ Mengarsipkan dokumen pendukung kinerja

### 1.2 Fitur Utama

-   🔐 **Sistem Autentikasi Multi-Role**
-   👥 **Manajemen Data Pegawai**
-   📋 **Perjanjian Kinerja Individual**
-   📊 **Penilaian Kinerja Bulanan**
-   📎 **Upload Dokumen Pendukung**
-   📄 **Generate PDF Laporan**
-   📧 **Notifikasi Email**
-   📈 **Dashboard Monitoring**
-   📥 **Import Data Massal**
-   🔍 **Pencarian dan Filter**

### 1.3 Peran Pengguna

| Peran                      | Deskripsi                | Akses                         |
| -------------------------- | ------------------------ | ----------------------------- |
| **Super Admin**            | Administrator sistem     | Full access ke semua fitur    |
| **Pejabat Penilai**        | Penilai kinerja pegawai  | Membuat PK, penilaian bulanan |
| **Atasan Pejabat Penilai** | Pimpinan yang menyetujui | Approve PK dan penilaian      |
| **Pegawai**                | Pegawai yang dinilai     | Self-service PK dan penilaian |

---

## 2. PERSIAPAN PENGGUNAAN

### 2.1 Persyaratan Sistem

**Perangkat Keras:**

-   Processor: Intel Core i3 atau setara
-   RAM: Minimum 4GB
-   Storage: Minimum 10GB ruang kosong

**Perangkat Lunak:**

-   Browser: Chrome 90+, Firefox 88+, Edge 90+
-   OS: Windows 10+, Linux, macOS
-   Internet: Koneksi stabil untuk akses online

**Akses Aplikasi:**

-   URL: `http://pkp-app.test` (sesuaikan dengan instalasi)
-   Email: Akun email resmi instansi
-   Password: Password yang diberikan admin

### 2.2 Instalasi dan Setup

**Untuk Administrator Sistem:**

1. **Persiapan Server:**

    ```bash
    # Install PHP 8.2+
    # Install MySQL 8.0+
    # Install Composer
    # Install Node.js & NPM
    ```

2. **Clone Repository:**

    ```bash
    git clone https://github.com/your-repo/app-pkp.git
    cd app-pkp
    ```

3. **Install Dependencies:**

    ```bash
    composer install
    npm install
    ```

4. **Konfigurasi Environment:**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Setup Database:**

    ```bash
    # Konfigurasi .env untuk database
    php artisan migrate
    php artisan db:seed
    ```

6. **Build Assets:**
    ```bash
    npm run build
    php artisan storage:link
    ```

### 2.3 Akses Aplikasi

1. **Buka browser** dan akses URL aplikasi
2. **Klik "Login"** di halaman utama
3. **Masukkan email dan password** yang telah diberikan
4. **Klik "Sign In"** untuk masuk ke dashboard

---

## 3. PANDUAN PENGGUNA SUPER ADMIN

### 3.1 Login dan Dashboard

**Langkah Login:**

1. Akses halaman login aplikasi
2. Masukkan email: `superadmin@pkp.test`
3. Masukkan password: `password`
4. Klik tombol "Sign In"

**Dashboard Overview:**

-   📊 **Statistik Keseluruhan:** Total pegawai, PK aktif, penilaian bulan ini
-   📋 **Perjanjian Pending:** Daftar PK yang menunggu approval
-   📈 **Grafik Kinerja:** Trend kinerja bulanan
-   🔔 **Notifikasi:** Update terbaru sistem

### 3.2 Manajemen Unit Kerja

**Menambah Unit Kerja Baru:**

1. Klik menu "Unit Kerja" → "Tambah Baru"
2. Isi form:
    - **Nama:** Nama unit kerja
    - **Kode:** Kode unik unit
    - **Alamat:** Alamat lengkap
    - **Kepala Unit:** Pilih pegawai (opsional)
3. Klik "Simpan"

**Mengedit Unit Kerja:**

1. Klik menu "Unit Kerja"
2. Cari unit yang akan diedit
3. Klik ikon "Edit" (✏️)
4. Update data yang diperlukan
5. Klik "Update"

### 3.3 Manajemen Pegawai

**Menambah Pegawai Baru:**

1. Klik menu "Pegawai" → "Tambah Baru"
2. Isi data pegawai:
    - **NIP:** Nomor Induk Pegawai
    - **Nama:** Nama lengkap
    - **Pangkat/Golongan:** Jabatan struktural
    - **Jabatan:** Posisi fungsional
    - **Unit Kerja:** Pilih dari dropdown
    - **Status:** Aktif/Inaktif/Pensiun
    - **Tanggal Masuk:** Tanggal mulai bekerja
3. Klik "Simpan"

**Import Data Pegawai:**

1. Klik menu "Pegawai" → "Import Data"
2. Download template Excel
3. Isi template dengan data pegawai
4. Upload file Excel
5. Klik "Import" dan tunggu proses selesai

### 3.4 Manajemen Perjanjian Kinerja

**Membuat Perjanjian Kinerja:**

1. Klik menu "Perjanjian Kinerja" → "Buat Baru"
2. Pilih pegawai dari dropdown
3. Isi tahun perjanjian
4. Tambah sasaran kegiatan:
    - **Sasaran Kegiatan:** Deskripsi target
    - **Indikator Kinerja:** Cara mengukur pencapaian
    - **Target Mutu:** Persentase target (0-100%)
    - **Target Kuantitas:** Jumlah target
    - **Satuan:** Unit pengukuran
    - **Bulan Penyelesaian:** Centang bulan target
5. Klik "Simpan sebagai Draft" atau "Ajukan Persetujuan"

**Menyetujui Perjanjian Kinerja:**

1. Klik menu "Perjanjian Kinerja" → "Menunggu Persetujuan"
2. Klik "Detail" pada PK yang akan disetujui
3. Review isi perjanjian
4. Klik "Setujui" atau "Tolak"
5. Jika tolak, isi alasan penolakan

### 3.5 Manajemen Penilaian Bulanan

**Melihat Penilaian Bulanan:**

1. Klik menu "Penilaian Bulanan"
2. Filter berdasarkan bulan, tahun, atau pegawai
3. Klik "Detail" untuk melihat detail penilaian

**Menyetujui Penilaian:**

1. Pada halaman detail penilaian
2. Review hasil penilaian dan dokumen pendukung
3. Klik "Setujui" untuk menyetujui
4. Sistem akan mengirim notifikasi email ke pegawai

### 3.6 Laporan dan Ekspor

**Generate Laporan PDF:**

1. Klik menu "Laporan"
2. Pilih jenis laporan:
    - **Perjanjian Kinerja:** Pilih pegawai dan tahun
    - **Penilaian Bulanan:** Pilih pegawai, bulan, tahun
    - **Rekapitulasi:** Pilih pegawai dan tahun
3. Klik "Generate PDF"
4. File PDF akan terdownload otomatis

---

## 4. PANDUAN PENGGUNA PEJABAT PENILAI

### 4.1 Login dan Dashboard

**Login sebagai Pejabat Penilai:**

-   Email: `pejabat@pkp.test`
-   Password: `password`

**Dashboard Features:**

-   📋 Daftar pegawai yang dibawahi
-   📊 Progress perjanjian kinerja
-   📅 Reminder penilaian bulanan
-   🔔 Notifikasi approval

### 4.2 Membuat Perjanjian Kinerja

**Untuk Pegawai Bawahan:**

1. Klik menu "Perjanjian Kinerja" → "Buat Baru"
2. Pilih pegawai dari dropdown (hanya pegawai bawahan)
3. Isi detail perjanjian kinerja
4. Klik "Simpan" atau "Ajukan Persetujuan"

### 4.3 Mengelola Penilaian Bulanan

**Melakukan Penilaian:**

1. Klik menu "Penilaian Bulanan" → "Buat Baru"
2. Pilih pegawai dan bulan penilaian
3. Isi realisasi untuk setiap target:
    - **Realisasi Kuantitas:** Jumlah yang dicapai
    - **Realisasi Mutu:** Persentase pencapaian
4. Sistem otomatis menghitung nilai capaian
5. Klik "Simpan sebagai Draft" atau "Submit"

### 4.4 Upload Dokumen

**Mengupload Dokumen Pendukung:**

1. Pada halaman penilaian bulanan
2. Klik tab "Dokumen"
3. Klik "Upload Dokumen"
4. Pilih file (max 10MB, format: PDF, DOC, DOCX, JPG, PNG)
5. Isi deskripsi dokumen
6. Klik "Upload"

---

## 5. PANDUAN PENGGUNA ATASAN PEJABAT PENILAI

### 5.1 Login dan Dashboard

**Login sebagai Atasan:**

-   Email: `atasan@pkp.test`
-   Password: `password`

**Dashboard Features:**

-   📋 Perjanjian kinerja menunggu approval
-   📊 Overview kinerja unit
-   📈 Statistik penilaian bulanan
-   🔔 Notifikasi urgent

### 5.2 Menyetujui Perjanjian Kinerja

**Proses Approval:**

1. Klik menu "Perjanjian Kinerja" → "Menunggu Persetujuan"
2. Klik "Detail" pada perjanjian
3. Review secara menyeluruh
4. Klik "Setujui" atau "Tolak dengan Alasan"
5. Sistem kirim email notifikasi ke pejabat penilai

### 5.3 Menyetujui Penilaian Bulanan

**Review Penilaian:**

1. Klik menu "Penilaian Bulanan" → "Menunggu Approval"
2. Klik "Detail" untuk review lengkap
3. Periksa dokumen pendukung
4. Klik "Setujui" untuk final approval

### 5.4 Monitoring Kinerja

**Melihat Laporan Kinerja:**

1. Klik menu "Laporan" → "Rekapitulasi"
2. Pilih pegawai dan tahun
3. Klik "Generate PDF" untuk laporan lengkap

---

## 6. PANDUAN PENGGUNA PEGAWAI

### 6.1 Login dan Dashboard

**Login sebagai Pegawai:**

-   Email: `pegawai@pkp.test` atau email pribadi
-   Password: `password` atau password yang dibuat

**Dashboard Features:**

-   📋 Status perjanjian kinerja pribadi
-   📊 Progress pencapaian bulanan
-   📅 Kalender penilaian
-   🔔 Notifikasi dari atasan

### 6.2 Membuat Perjanjian Kinerja Sendiri

**Self-Service PK:**

1. Klik menu "Perjanjian Kinerja" → "Buat Baru"
2. Data pegawai otomatis terisi (hanya data pribadi)
3. Isi sasaran kegiatan untuk tahun berjalan
4. Klik "Simpan Draft" atau "Ajukan Persetujuan"

### 6.3 Melakukan Penilaian Bulanan

**Self-Assessment:**

1. Klik menu "Penilaian Bulanan" → "Penilaian Saya"
2. Pilih bulan yang akan dinilai
3. Isi realisasi pencapaian:
    - **Realisasi Kuantitas:** Jumlah yang dicapai
    - **Realisasi Mutu:** Persentase pencapaian
4. Upload dokumen pendukung
5. Klik "Submit untuk Approval"

### 6.4 Mengupload Dokumen Pendukung

**Upload Bukti Kinerja:**

1. Pada form penilaian bulanan
2. Klik "Tambah Dokumen"
3. Pilih file dari komputer
4. Isi keterangan dokumen
5. Klik "Upload"

---

## 7. PANDUAN IMPORT DATA

### 7.1 Template Import Pegawai

**Download Template:**

1. Klik menu "Pegawai" → "Import Data"
2. Klik "Download Template"
3. File Excel akan terdownload

**Struktur Template:**

```csv
nip,nama,pangkat_golongan,jabatan,unit_kerja_id,status,tanggal_masuk
198501012010011001,AHMAD RAHMAN,Pembina Tk. I / IV.b,Juru Sita,1,active,2010-01-01
```

**Keterangan Kolom:**

-   **nip:** Nomor Induk Pegawai (wajib, unik)
-   **nama:** Nama lengkap pegawai (wajib)
-   **pangkat_golongan:** Pangkat/Golongan (opsional)
-   **jabatan:** Jabatan fungsional (opsional)
-   **unit_kerja_id:** ID unit kerja (wajib, harus ada di sistem)
-   **status:** active/inactive/retired (wajib)
-   **tanggal_masuk:** Format YYYY-MM-DD (opsional)

### 7.2 Proses Import

**Langkah Import:**

1. Buka template Excel yang telah didownload
2. Isi data pegawai sesuai format
3. Pastikan unit_kerja_id valid (lihat daftar unit kerja)
4. Simpan file Excel
5. Upload file melalui menu Import
6. Klik "Import" dan tunggu proses selesai

### 7.3 Validasi Data

**Error yang Mungkin Terjadi:**

-   **NIP sudah terdaftar:** NIP harus unik
-   **Unit kerja tidak ditemukan:** Pastikan ID unit kerja valid
-   **Format tanggal salah:** Gunakan format YYYY-MM-DD
-   **File kosong:** Pastikan ada data yang diisi

---

## 8. PANDUAN PELAPORAN

### 8.1 Laporan Perjanjian Kinerja

**Generate Laporan PK:**

1. Klik menu "Laporan" → "Perjanjian Kinerja"
2. Pilih pegawai dari dropdown
3. Pilih tahun perjanjian
4. Klik "Generate PDF"

**Isi Laporan:**

-   Header instansi
-   Data pegawai lengkap
-   Tabel sasaran kegiatan
-   Target kuantitas dan mutu
-   Bulan penyelesaian
-   Tanda tangan pejabat

### 8.2 Laporan Penilaian Bulanan

**Generate Laporan Penilaian:**

1. Klik menu "Laporan" → "Penilaian Bulanan"
2. Pilih pegawai, bulan, dan tahun
3. Klik "Generate PDF"

**Isi Laporan:**

-   Data pegawai dan bulan penilaian
-   Tabel perbandingan target vs realisasi
-   Nilai capaian otomatis
-   Grade kinerja
-   Catatan penilaian
-   Tanda tangan pejabat

### 8.3 Laporan Rekapitulasi

**Generate Laporan Tahunan:**

1. Klik menu "Laporan" → "Rekapitulasi"
2. Pilih pegawai dan tahun
3. Klik "Generate PDF"

**Isi Laporan:**

-   Summary kinerja tahunan
-   Tabel rekapitulasi bulanan
-   Rata-rata nilai capaian
-   Grade keseluruhan
-   Rekomendasi pengembangan

---

## 9. TROUBLESHOOTING

### 9.1 Masalah Login

**Error: "Email atau password salah"**

-   ✅ Pastikan email dan password benar
-   ✅ Periksa caps lock
-   ✅ Reset password jika lupa

**Error: "Akun tidak aktif"**

-   ✅ Hubungi administrator sistem
-   ✅ Akun perlu diaktifkan oleh admin

### 9.2 Error Upload File

**Error: "File terlalu besar"**

-   ✅ Ukuran maksimal 10MB per file
-   ✅ Kompres file jika terlalu besar

**Error: "Format file tidak didukung"**

-   ✅ Format yang didukung: PDF, DOC, DOCX, JPG, PNG
-   ✅ Konversi file ke format yang didukung

### 9.3 Error Import Data

**Error: "NIP sudah terdaftar"**

-   ✅ Periksa NIP di database
-   ✅ Gunakan NIP yang belum terdaftar

**Error: "Unit kerja tidak ditemukan"**

-   ✅ Pastikan unit kerja sudah dibuat
-   ✅ Gunakan ID unit kerja yang valid

### 9.4 Error Generate PDF

**Error: "PDF gagal dibuat"**

-   ✅ Periksa koneksi internet
-   ✅ Pastikan data lengkap
-   ✅ Coba generate ulang

---

## 10. FAQ

### **Q: Bagaimana cara reset password?**

**A:** Hubungi administrator sistem untuk reset password atau gunakan fitur "Lupa Password" jika tersedia.

### **Q: Berapa lama proses approval?**

**A:** Proses approval biasanya memakan waktu 1-3 hari kerja tergantung beban kerja atasan.

### **Q: Bagaimana cara mengubah data pribadi?**

**A:** Untuk pegawai: Hubungi admin HRD. Untuk admin: Edit melalui menu "Pegawai".

### **Q: Apakah data tersimpan dengan aman?**

**A:** Ya, semua data dienkripsi dan disimpan dengan standar keamanan tinggi.

### **Q: Bagaimana cara export data?**

**A:** Gunakan menu "Laporan" untuk export PDF atau hubungi admin untuk export data mentah.

### **Q: Siapa yang bisa melihat data saya?**

**A:** Atasan langsung, pejabat penilai, dan administrator sistem sesuai dengan role dan permission.

### **Q: Bagaimana cara upload dokumen?**

**A:** Pada halaman penilaian bulanan, klik tab "Dokumen" lalu "Upload Dokumen".

### **Q: Format file apa yang didukung?**

**A:** PDF, DOC, DOCX, JPG, PNG dengan ukuran maksimal 10MB per file.

---

## 📞 KONTAK SUPPORT

**Administrator Sistem:**

-   Email: admin@pkp-app.test
-   Telepon: (021) 123-4567

**Helpdesk Teknis:**

-   Email: support@pkp-app.test
-   Telepon: (021) 123-4568

**Jam Operasional:**

-   Senin - Jumat: 08:00 - 16:00 WIB
-   Sabtu: 08:00 - 12:00 WIB
-   Minggu: Tutup

---

## 📋 LOG PERUBAHAN

| Versi | Tanggal    | Perubahan                     |
| ----- | ---------- | ----------------------------- |
| 1.0.0 | 2025-01-14 | Release awal aplikasi         |
| 1.0.1 | 2025-01-14 | Perbaikan import pegawai      |
| 1.0.2 | 2025-01-14 | Penambahan fitur self-service |

---

**📖 Manual ini dibuat untuk memudahkan penggunaan Aplikasi APP PKP. Untuk pertanyaan lebih lanjut, silakan hubungi tim support.**

**🏛️ Pengadilan Agama - Sistem Manajemen Kinerja Pegawai**
**📧 support@pkp-app.test | 📞 (021) 123-4567**
