# 📘 Project README

## 🧩 Deskripsi Project

Project ini adalah aplikasi web berbasis Laravel yang menyediakan sistem **Login dengan Custom Token**, **Manajemen User**, **Manajemen Role**, **Fitur Filter & Export Data**, serta **Upload & Manajemen File via FTP**. Proyek ini dibuat untuk mempermudah pengelolaan user dan role, melakukan pencarian, dan mengelola file CSV yang disimpan di server FTP.

Aplikasi dibangun dengan konsep yang mudah dipahami, interaktif, dan tetap mengikuti standar alur kerja aplikasi berbasis Laravel.

---

## 🚀 Fitur Utama

### 1. **Sistem Login (Custom Token per Session)**

* Token tidak disimpan di database.
* Ditambahkan mekanisme login dan logout standar.
* Menampilkan "Selamat datang, {{ user }}" pada pojok kanan atas halaman.

### 2. **Master User (Admin Kelola User)**

Admin dapat:

* Membuat user baru
* Mengatur Username, Password, Role, Email
* Mengirim email otomatis setelah user berhasil dibuat
* Mengedit user & role tertentu

### 3. **Master Role**

* Menampilkan seluruh data role
* Tidak bisa menghapus role yang sedang dipakai oleh user
* Jika role di-update, nama di Master User akan ikut berubah
* Role `admin` **tidak bisa dihapus dan tidak dapat mengubah nama — hanya deskripsi**

### 4. **Fitur Filter & Export Data**

Filter berdasarkan:

* Username (dengan validasi)
* Creation date

Hasil filter ditampilkan dalam tabel berisi:

* Username
* Role
* ID User
* Email
* Creation Date

Export:

* **PDF** (berisi tabel lengkap)
* **CSV** (delimiter `|`)
* Nama file: `pdf/CSV_<tanggal-dengan-detik>_<jenisDokumen>.pdf/csv`

Termasuk loading animation saat proses.

### 5. **Admin Edit Dirinya Sendiri**

* Jika admin mengubah role-nya sendiri, sistem otomatis melakukan logout dan kembali ke halaman login.

### 6. **Upload CSV ke FTP + CRUD File**

Menu baru meliputi:

* Tombol upload file CSV → otomatis dikirim ke FTP server
* Tabel daftar file dari FTP (ditampilkan via `ftp_nlist`)
* Fitur delete file

---

## 📦 Struktur Menu Aplikasi

```
Dashboard
├── Login
├── Master User
│     ├── Tambah User
│     ├── Edit User
│     └── List User
├── Master Role
│     ├── Tambah Role
│     ├── Edit Role
│     └── List Role
├── Filter & Export
│     ├── Filter Username & Date
│     ├── Export PDF
│     └── Export CSV
└── File Manager (FTP)
      ├── Upload CSV
      ├── List File CSV
      └── Delete File
```

---

## ⚠️ Hal Penting yang Harus Diperhatikan

### 🔐 **Sistem Role**

* Role "admin" tidak boleh diubah namanya
* Role yang sedang digunakan user tidak boleh dihapus
* Update role akan mengupdate juga di tabel user

### 📤 **Upload File ke FTP**

* Pastikan credential FTP benar
* Format file wajib CSV

### 📄 **Export File**

* Pastikan folder penyimpanan export dapat diakses oleh Laravel (permission write)

### ✉️ **Email**

* Pastikan SMTP sudah dikonfigurasi di `.env`

---

## 🛠️ Cara Install Project

### 1. Clone Repository

```bash
git clone <url-repository>
cd project-folder
```

### 2. Install Dependencies

```bash
composer install
npm install
npm run build
```

### 3. Copy & Setup Environment

```bash
cp .env.example .env
```

Edit `.env`:

* Database
* SMTP email
* FTP server
* App URL

### 4. Generate Key

```bash
php artisan key:generate
```

### 5. Migrasi Database

```bash
php artisan migrate --seed
```

(Seeder akan membuat role admin default)

### 6. Jalankan Server

```bash
php artisan serve
```

---

## 🧪 Cara Menggunakan Aplikasi

### ✔ 1. Login menggunakan akun admin default

### ✔ 2. Buka menu "Master Role" → tambahkan role jika diperlukan

### ✔ 3. Buka menu "Master User" → daftarkan user baru

* Email akan terkirim otomatis

### ✔ 4. Buka menu "Filter & Export"

* Masukkan username valid → search
* Pilih creation date
* Export ke PDF atau CSV

### ✔ 5. Buka menu "File Manager (FTP)"

* Upload file CSV
* File akan muncul di tabel
* Hapus file dari FTP jika diperlukan

---

## 📮 Kontak

Jika ingin diskusi atau mengalami masalah pada aplikasi, silakan hubungi developer melalui GitHub Issues atau email terkait.

---

## 📄 Lisensi

Project ini menggunakan lisensi standar MIT (jika diperlukan).
