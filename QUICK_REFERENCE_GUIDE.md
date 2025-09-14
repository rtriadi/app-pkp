# 🚀 PANDUAN CEPAT APP PKP

## Sistem Manajemen Kinerja Pegawai

---

## 👤 LOGIN AKUN DEFAULT

| Role            | Email                 | Password   |
| --------------- | --------------------- | ---------- |
| Super Admin     | `superadmin@pkp.test` | `password` |
| Pejabat Penilai | `pejabat@pkp.test`    | `password` |
| Atasan Pejabat  | `atasan@pkp.test`     | `password` |
| Pegawai         | `pegawai@pkp.test`    | `password` |

---

## 📋 WORKFLOW CEPAT

### **Untuk Pegawai:**

1. **Login** → Dashboard
2. **Buat PK** → Menu "Perjanjian Kinerja" → "Buat Baru"
3. **Isi Sasaran** → Tambah target kegiatan
4. **Submit** → "Ajukan Persetujuan"
5. **Tunggu Approval** → Email notifikasi
6. **Penilaian Bulanan** → Isi realisasi tiap bulan
7. **Upload Dokumen** → Bukti pencapaian

### **Untuk Pejabat Penilai:**

1. **Login** → Dashboard
2. **Buat PK** → Untuk pegawai bawahan
3. **Review PK** → Approve/reject
4. **Penilaian Bulanan** → Input realisasi
5. **Upload Dokumen** → Bukti pendukung

### **Untuk Atasan:**

1. **Login** → Dashboard
2. **Approve PK** → Menu "Perjanjian Kinerja"
3. **Approve Penilaian** → Menu "Penilaian Bulanan"
4. **Monitoring** → Laporan dan statistik

---

## ⚡ SHORTCUT MENU

| Menu                   | Super Admin | Pejabat Penilai | Atasan     | Pegawai   |
| ---------------------- | ----------- | --------------- | ---------- | --------- |
| **Unit Kerja**         | ✅ Full     | ❌              | ❌         | ❌        |
| **Pegawai**            | ✅ Full     | ✅ Import       | ❌         | ❌        |
| **Perjanjian Kinerja** | ✅ Full     | ✅ Create       | ✅ Approve | ✅ Self   |
| **Penilaian Bulanan**  | ✅ Full     | ✅ Create       | ✅ Approve | ✅ Self   |
| **Laporan**            | ✅ Full     | ✅ View         | ✅ View    | ✅ Self   |
| **Dokumen**            | ✅ Full     | ✅ Upload       | ✅ View    | ✅ Upload |

---

## 📊 STATUS WORKFLOW

### **Perjanjian Kinerja:**

```
Draft → Pending → Approved/Rejected
```

### **Penilaian Bulanan:**

```
Draft → Submitted → Approved
```

---

## 📄 TEMPLATE IMPORT

**File:** `template_import_pegawai.xlsx`

**Format CSV:**

```csv
nip,nama,pangkat_golongan,jabatan,unit_kerja_id,status,tanggal_masuk
198501012010011001,AHMAD RAHMAN,Pembina Tk. I / IV.b,Juru Sita,1,active,2010-01-01
```

**Catatan:**

-   `unit_kerja_id` harus sudah ada di sistem
-   Status: `active`, `inactive`, `retired`
-   Tanggal format: `YYYY-MM-DD`

---

## 📧 EMAIL NOTIFICATION

**Penerima Email:**

-   ✅ Pegawai → Saat PK disetujui/ditolak
-   ✅ Pejabat Penilai → Saat PK perlu review
-   ✅ Atasan → Saat penilaian perlu approval

---

## 🔧 TROUBLESHOOTING CEPAT

| Masalah                | Solusi                     |
| ---------------------- | -------------------------- |
| **Login gagal**        | Cek email & password       |
| **File terlalu besar** | Max 10MB, kompres file     |
| **Format file salah**  | Gunakan PDF, DOC, JPG, PNG |
| **Import gagal**       | Cek unit_kerja_id valid    |
| **PDF error**          | Refresh halaman, coba lagi |

---

## 📞 KONTAK DARURAT

-   **Admin:** admin@pkp-app.test
-   **Support:** support@pkp-app.test
-   **Telp:** (021) 123-4567

---

## 🎯 TIPS PENGGUNAAN

### **Pegawai:**

-   ✅ Buat PK di awal tahun
-   ✅ Update penilaian tepat waktu
-   ✅ Upload dokumen pendukung
-   ✅ Monitor progress kinerja

### **Pejabat Penilai:**

-   ✅ Review PK secara berkala
-   ✅ Berikan feedback konstruktif
-   ✅ Pastikan dokumen lengkap
-   ✅ Approve tepat waktu

### **Atasan:**

-   ✅ Monitor kinerja unit
-   ✅ Approve dengan pertimbangan
-   ✅ Berikan saran perbaikan
-   ✅ Generate laporan berkala

---

## 📈 METRIK KINERJA

**Formula Perhitungan:**

```
Nilai Capaian = (Target + Realisasi) / 2
```

**Grade Mapping:**

-   91-100: **Sangat Baik**
-   76-90: **Baik**
-   61-75: **Cukup**
-   51-60: **Kurang**
-   0-50: **Sangat Kurang**

---

## 🔄 ALUR PROSES

```
Pegawai → Buat PK → Pejabat Penilai → Review → Atasan → Approve → Aktif
Pegawai → Isi Penilaian → Pejabat Penilai → Review → Atasan → Final Approve
```

---

**📖 Untuk panduan lengkap, lihat `USER_MANUAL_APP_PKP.md`**

**🏛️ Pengadilan Agama - APP PKP**
